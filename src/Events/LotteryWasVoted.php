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
use Nodeloc\Lottery\LotteryVote;

class LotteryWasVoted
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
     * @var LotteryVote
     */
    public $vote;

    /**
     * @var bool
     */
    public $changed;

    /**
     * LotteryWasCreated constructor.
     *
     * @param User     $actor
     * @param Lottery     $lottery
     * @param LotteryVote $vote
     * @param bool     $changed
     */
    public function __construct(User $actor, Lottery $lottery, LotteryVote $vote, $changed = false)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
        $this->vote = $vote;
        $this->changed = $changed;
    }
}
