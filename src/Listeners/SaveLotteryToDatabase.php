<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Listeners;

use Flarum\Foundation\ValidationException;
use Flarum\Post\Event\Saving;
use Nodeloc\Lottery\Commands\CreateLottery;
use Nodeloc\Lottery\Validators\LotteryOptionValidator;
use Nodeloc\Lottery\Validators\LotteryValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class SaveLotteryToDatabase
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
     * @var \Flarum\Bus\Dispatcher
     */
    protected $bus;

    public function __construct(LotteryValidator $validator, LotteryOptionValidator $optionValidator, Dispatcher $events, \Flarum\Bus\Dispatcher $bus)
    {
        $this->validator = $validator;
        $this->optionValidator = $optionValidator;
        $this->events = $events;
        $this->bus = $bus;
    }

    public function handle(Saving $event)
    {
        if ($event->post->exists || !isset($event->data['attributes']['lottery'])) {
            return;
        }

        // 'assertCan' throws a generic no permission error, but we want to be more specific.
        // There are a lot of different reasons why a user might not be able to post a discussion.
        if ($event->actor->cannot('startLottery', $event->post)) {
            $translator = resolve(TranslatorInterface::class);

            throw new ValidationException([
                'lottery' => $translator->trans('nodeloc-lottery.forum.composer_discussion.no_permission_alert'),
            ]);
        }

        $attributes = (array) $event->data['attributes']['lottery'];

        $this->bus->dispatch(
            new CreateLottery(
                $event->actor,
                $event->post,
                $attributes,
                function (callable $callback) use ($event) {
                    $event->post->afterSave($callback);
                }
            )
        );
    }
}
