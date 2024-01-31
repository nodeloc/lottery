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
use Illuminate\Support\Collection;

class LotteryVotesChanged
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
     * @var Collection
     */
    public $unvotedOptionIds;

    /**
     * @var Collection
     */
    public $votedOptionIds;

    /**
     * LotteryWasCreated constructor.
     *
     * @param User       $actor
     * @param Lottery       $lottery
     * @param Collection $unvotedOptionIds
     * @param Collection $votedOptionIds
     */
    public function __construct(User $actor, Lottery $lottery, Collection $unvotedOptionIds, Collection $votedOptionIds)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
        $this->unvotedOptionIds = $unvotedOptionIds;
        $this->votedOptionIds = $votedOptionIds;
    }
}
