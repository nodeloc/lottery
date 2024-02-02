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

        if (isset($attributes['prizes'])) {
            $lottery->prizes = $attributes['prizes'];
        }

        if (isset($attributes['price'])) {
            $lottery->price = $attributes['price'];
        }
        if (isset($attributes['amount'])) {
            $lottery->amount = $attributes['amount'];
        }
        if (isset($attributes['min_participants'])) {
            $lottery->min_participants = $attributes['min_participants'];
        }
        if (isset($attributes['max_participants'])) {
            $lottery->max_participants = $attributes['max_participants'];
        }

        if (isset($attributes['endDate'])) {
            $endDate = $attributes['endDate'];

            if (is_string($endDate)) {
                $date = Carbon::parse($endDate);

                if (!$lottery->hasEnded() && $date->isFuture() && ($lottery->end_date === null || $lottery->end_date->lessThanOrEqualTo($date))) {
                    $lottery->end_date = $date->setTimezone('Asia/Shanghai');
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
                'operator_type'   => Arr::get($opt, 'attributes.operator_type'),
                'operator' => Arr::get($opt, 'attributes.operator'),
                'operator_value' => Arr::get($opt, 'attributes.operator_value'),
            ];

            $this->optionValidator->assertValid($optionAttributes);

            $lottery->options()->updateOrCreate([
                'id' => $id,
            ], [
                'operator_type'    => Arr::get($optionAttributes, 'operator_type'),
                'operator' => Arr::get($optionAttributes, 'operator'),
                'operator_value' => Arr::get($optionAttributes, 'operator_value'),
            ]);
        }

        return $lottery;
    }
}
