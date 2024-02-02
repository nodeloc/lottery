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
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Nodeloc\Lottery\Events\LotteryWasCreated;
use Nodeloc\Lottery\Events\SavingLotteryAttributes;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\LotteryOption;
use Nodeloc\Lottery\Validators\LotteryOptionValidator;
use Nodeloc\Lottery\Validators\LotteryValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class CreateLotteryHandler
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
     * @var PostRepository
     */
    protected $posts;

    public function __construct(PostRepository $posts, LotteryValidator $validator, LotteryOptionValidator $optionValidator, Dispatcher $events, SettingsRepositoryInterface $settings)
    {
        $this->validator = $validator;
        $this->optionValidator = $optionValidator;
        $this->events = $events;
        $this->settings = $settings;
        $this->posts = $posts;
    }

    public function handle(CreateLottery $command)
    {
        $command->actor->assertCan('startLottery', $command->post);

        $attributes = $command->data;

        // Ideally we would use some JSON:API relationship syntax, but it's just too complicated with Flarum to generate the correct JSON payload
        // Instead we just pass an array of option objects that are each a set of key-value pairs for the option attributes
        // This is also the same syntax that always used by EditLotteryHandler
        $rawOptionsData = Arr::get($attributes, 'options');
        $optionsData = [];

        if (is_array($rawOptionsData)) {
            foreach ($rawOptionsData as $rawOptionData) {
                $optionsData[] = [
                    'operator_type'   => Arr::get($rawOptionData, 'operator_type'),
                    'operator' => Arr::get($rawOptionData, 'operator'),
                    'operator_value' => Arr::get($rawOptionData, 'operator_value'),
                ];
            }
        }

        $this->validator->assertValid($attributes);

        foreach ($optionsData as $optionData) {
            // It is guaranteed all keys exist in the array because $optionData is manually created above
            // This ensures every attribute will be validated (Flarum doesn't validate missing keys)
            $this->optionValidator->assertValid($optionData);
        }
        return ($command->saveLotteryOn)(function () use ($optionsData, $attributes, $command) {
            $endDate = Arr::get($attributes, 'endDate');
            $carbonDate = Carbon::parse($endDate);

            if (!$carbonDate->isFuture()) {
                $carbonDate = null;
            }

            $lottery = Lottery::build(
                Arr::get($attributes, 'prizes'),
                $command->post->id,
                $command->actor->id,
                $carbonDate != null ? $carbonDate->utc() : null,
                Arr::get($attributes, 'price'),
                Arr::get($attributes, 'amount'),
                Arr::get($attributes, 'min_participants'),
                Arr::get($attributes, 'max_participants'),
                Arr::get($attributes, 'can_cancel_enter'),
            );

            $this->events->dispatch(new SavingLotteryAttributes($command->actor, $lottery, $attributes, $attributes));

            $lottery->save();

            $this->events->dispatch(new LotteryWasCreated($command->actor, $lottery));

            foreach ($optionsData as $optionData) {
                $option = LotteryOption::build(Arr::get($optionData, 'operator_type'), Arr::get($optionData, 'operator'),Arr::get($optionData, 'operator_value'));
                $lottery->options()->save($option);
            }

            return $lottery;
        });
    }
}
