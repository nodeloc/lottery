<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Events;

use Flarum\User\User;
use Nodeloc\Lottery\Lottery;

/**
 * Dispatched while a lottery is being saved
 * This event is triggered in both SaveLotteryToDatabase and EditLotteryHandler, which don't have the same data format!
 * For this reason the "attributes" part of the JSON:API payload is provided as a separate attribute since it's almost identical for both situations.
 *
 * The create/edit authorization has already been performed when this event is dispatched, so it doesn't need to be checked again
 *
 * You should not throw any exception if the lottery doesn't exist because this happens after the post has already been created and would break email and other extensions
 */
class SavingLotteryAttributes
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Lottery
     */
    public $lottery;

    /**
     * @var array
     */
    public $attributes;

    /**
     * @var array
     */
    public $data;

    /**
     * @param User  $actor
     * @param Lottery  $lottery
     * @param array $attributes
     * @param array $data
     */
    public function __construct(User $actor, Lottery $lottery, array $attributes, array $data)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
        $this->attributes = $attributes;
        $this->data = $data;
    }
}
