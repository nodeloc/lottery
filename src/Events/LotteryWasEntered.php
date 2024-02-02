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

use AllowDynamicProperties;
use Flarum\User\User;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\LotteryParticipants;

class LotteryWasEntered
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
     * @var LotteryParticipants
     */
    public $participants;

    /**
     * @var bool
     */
    public $status;

    /**
     * LotteryWasCreated constructor.
     *
     * @param User     $actor
     * @param Lottery     $lottery
     * @param LotteryParticipants $participants
     * @param bool $status
     */
    public function __construct(User $actor, Lottery $lottery, LotteryParticipants $participants, $status = False)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
        $this->participants = $participants;
        $this->status = $status;
    }
}
