<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Commands;

use Flarum\Foundation\ErrorHandling\Reporter;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Nodeloc\Lottery\Events\LotteryVotesChanged;
use Nodeloc\Lottery\Events\LotteryWasVoted;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\LotteryRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Pusher;

class MultipleVotesLotteryHandler
{
    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Factory
     */
    private $validation;

    /**
     * @var ConnectionResolverInterface
     */
    private $db;

    /**
     * @var LotteryRepository
     */
    private $lottery;

    /**
     * @param Dispatcher                  $events
     * @param SettingsRepositoryInterface $settings
     * @param Container                   $container
     */
    public function __construct(LotteryRepository $lottery, Dispatcher $events, SettingsRepositoryInterface $settings, Container $container, Factory $validation, ConnectionResolverInterface $db)
    {
        $this->lottery = $lottery;
        $this->events = $events;
        $this->settings = $settings;
        $this->container = $container;
        $this->validation = $validation;
        $this->db = $db;
    }

    /**
     * @throws PermissionDeniedException
     * @throws ValidationException
     */
    public function handle(MultipleVotesLottery $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $lottery = $this->lottery->findOrFail($command->lotteryId, $actor);

        $actor->assertCan('vote', $lottery);

        $optionIds = Arr::get($data, 'optionIds');
        $options = $lottery->options;
        $myVotes = $lottery->myVotes($actor)->get();

        $maxVotes = $lottery->allow_multiple_votes ? $lottery->max_votes : 1;

        if ($maxVotes == 0) {
            $maxVotes = $options->count();
        }

        $validator = $this->validation->make([
            'options' => $optionIds,
        ], [
            'options' => [
                'present',
                'array',
                'max:'.$maxVotes,
                function ($attribute, $value, $fail) use ($options) {
                    foreach ($value as $optionId) {
                        if (!$options->contains('id', $optionId)) {
                            $fail('Invalid option ID.');
                        }
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException(['options' => $validator->getMessageBag()->first('options')]);
        }

        $deletedVotes = $myVotes->filter(function ($vote) use ($optionIds) {
            return !in_array((string) $vote->option_id, $optionIds);
        });
        $newOptionIds = collect($optionIds)->filter(function ($optionId) use ($myVotes) {
            return !$myVotes->contains('option_id', $optionId);
        });

        /** @phpstan-ignore-next-line */
        $this->db->transaction(function () use ($myVotes, $options, $newOptionIds, $deletedVotes, $lottery, $actor) {
            // Unvote options
            if ($deletedVotes->isNotEmpty()) {
                $lottery->myVotes($actor)->whereIn('id', $deletedVotes->pluck('id'))->delete();
                $deletedVotes->each->unsetRelation('option');

                $myVotes->forget($deletedVotes->pluck('id')->toArray());
            }

            // Vote options
            $newOptionIds->each(function ($optionId) use ($myVotes, $lottery, $actor) {
                $vote = $lottery->votes()->create([
                    'user_id'   => $actor->id,
                    'option_id' => $optionId,
                ]);

                $myVotes->push($vote);
            });

            // Update vote counts of options & lottery
            $changedOptions = $options->whereIn('id', $deletedVotes->pluck('option_id')->toArray())
                ->concat($options->whereIn('id', $newOptionIds->toArray()));

            $changedOptions->each->refreshVoteCount()->each->save();

            if ($deletedVotes->isNotEmpty() || $newOptionIds->isNotEmpty()) {
                $lottery->refreshVoteCount()->save();
            }
        });

        $currentVoteOptions = $options->whereIn('id', $myVotes->pluck('option_id'))->except($deletedVotes->pluck('option_id')->toArray());
        $deletedVoteOptions = $options->whereIn('id', $deletedVotes->pluck('option_id'));

        // Legacy event for backward compatibility with single-vote lottery. Can be removed in breaking release.
        if (!$lottery->allow_multiple_votes && !$myVotes->isEmpty()) {
            $this->events->dispatch(new LotteryWasVoted($actor, $lottery, $myVotes->first(), !$deletedVotes->isEmpty() && !$newOptionIds->isEmpty()));
        }

        $this->events->dispatch(new LotteryVotesChanged($actor, $lottery, $deletedVoteOptions->pluck('option.id'), $newOptionIds));

        try {
            $changedOptionsIds = $currentVoteOptions->concat($deletedVoteOptions)->pluck('id');
            $changedOptions = $options->whereIn('id', $changedOptionsIds);

            $this->pushUpdatedOptions($lottery, $changedOptions);
        } catch (\Exception $e) {
            // We don't want to display an error to the user if the websocket functionality fails.
            $reporters = resolve('container')->tagged(Reporter::class);

            foreach ($reporters as $reporter) {
                $reporter->report($e);
            }
        }

        return $lottery;
    }

    /**
     * Pushes an updated option through websocket.
     *
     * @param \Illuminate\Support\Collection $options
     */
    public function pushUpdatedOptions(Lottery $lottery, $options)
    {
        if ($pusher = $this->getPusher()) {
            $pusher->trigger('public', 'updatedPollOptions', [
                'lotteryId'          => $lottery->id,
                'lotteryVoteCount'   => $lottery->vote_count,
                'options'         => $options->pluck('vote_count', 'id')->toArray(),
            ]);
        }
    }

    private function getPusher()
    {
        return self::pusher($this->container, $this->settings);
    }

    /**
     * @return bool|\Illuminate\Foundation\Application|mixed|Pusher
     */
    public static function pusher(Container $container, SettingsRepositoryInterface $settings)
    {
        if (!class_exists(Pusher::class)) {
            return false;
        }

        if ($container->bound(Pusher::class)) {
            return $container->make(Pusher::class);
        } else {
            $options = [];

            if ($cluster = $settings->get('flarum-pusher.app_cluster')) {
                $options['cluster'] = $cluster;
            }

            $appKey = $settings->get('flarum-pusher.app_key');
            $appSecret = $settings->get('flarum-pusher.app_secret');
            $appId = $settings->get('flarum-pusher.app_id');

            // Don't create a Pusher instance if we don't have the required credentials.
            // This is to prevent errors when e.g. the Pusher extension is disabled, since Pusher seems
            // to throw a deprecated warning when trying to create a hash from a null secret.
            if (!$appKey || !$appSecret || !$appId) {
                return false;
            }

            return new Pusher(
                $appKey,
                $appSecret,
                $appId,
                $options
            );
        }
    }
}
