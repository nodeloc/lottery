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

use Carbon\Carbon;
use Flarum\Settings\SettingsRepositoryInterface;
use Nodeloc\Lottery\Events\SavingLotteryAttributes;
use Nodeloc\Lottery\LotteryRepository;
use Nodeloc\Lottery\Validators\LotteryOptionValidator;
use Nodeloc\Lottery\Validators\LotteryValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EditLotteryHandler
{
    /**
     * @var LotteryValidator
     */
    protected $validator;

    /**
     * @var LotteryOptionValidator
     */
    protected $optionValidator;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var LotteryRepository
     */
    protected $lottery;

    public function __construct(LotteryRepository $lottery, LotteryValidator $validator, LotteryOptionValidator $optionValidator, Dispatcher $events, SettingsRepositoryInterface $settings)
    {
        $this->validator = $validator;
        $this->optionValidator = $optionValidator;
        $this->events = $events;
        $this->settings = $settings;
        $this->lottery = $lottery;
    }

    public function handle(EditLottery $command)
    {
        $lottery = $this->lottery->findOrFail($command->lotteryId, $command->actor);

        $command->actor->assertCan('edit', $lottery);

        $attributes = (array) Arr::get($command->data, 'attributes');
        $options = collect(Arr::get($attributes, 'options', []));

        $this->validator->assertValid($attributes);

        if (isset($attributes['question'])) {
            $lottery->question = $attributes['question'];
        }

        foreach (['publicPoll', 'allowMultipleVotes', 'hideVotes', 'allowChangeVote'] as $key) {
            if (isset($attributes[$key])) {
                $lottery->settings[Str::snake($key)] = (bool) $attributes[$key];
            }
        }

        if (isset($attributes['maxVotes'])) {
            $maxVotes = (int) $attributes['maxVotes'];
            $lottery->settings['max_votes'] = min(max($maxVotes, 0), $options->count());
        }

        if (isset($attributes['endDate'])) {
            $endDate = $attributes['endDate'];

            if (is_string($endDate)) {
                $date = Carbon::parse($endDate);

                if (!$lottery->hasEnded() && $date->isFuture() && ($lottery->end_date === null || $lottery->end_date->lessThanOrEqualTo($date))) {
                    $lottery->end_date = $date->utc();
                }
            } elseif (is_bool($endDate) && !$endDate) {
                $lottery->end_date = null;
            }
        }

        $this->events->dispatch(new SavingLotteryAttributes($command->actor, $lottery, $attributes, $command->data));

        $lottery->save();

        // remove options not passed if 2 or more are
        if ($options->isNotEmpty() && $options->count() >= 2) {
            $ids = $options->pluck('id')->whereNotNull()->toArray();

            $lottery->options()->whereNotIn('id', $ids)->delete();
        }

        // update + add new options
        foreach ($options as $key => $opt) {
            $id = Arr::get($opt, 'id');

            $optionAttributes = [
                'answer'   => Arr::get($opt, 'attributes.answer'),
                'imageUrl' => Arr::get($opt, 'attributes.imageUrl') ?: null,
            ];

            if (!$this->settings->get('nodeloc-lottery.allowOptionImage')) {
                unset($optionAttributes['imageUrl']);
            }

            $this->optionValidator->assertValid($optionAttributes);

            $lottery->options()->updateOrCreate([
                'id' => $id,
            ], [
                'answer'    => Arr::get($optionAttributes, 'answer'),
                'image_url' => Arr::get($optionAttributes, 'imageUrl'),
            ]);
        }

        return $lottery;
    }
}
