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

class LotteryCancelEnter
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
    public $unenterdOptionIds;

    /**
     * @var Collection
     */
    public $enterdOptionIds;

    /**
     * LotteryWasCreated constructor.
     *
     * @param User       $actor
     * @param Lottery       $lottery
     * @param Collection $unenterdOptionIds
     * @param Collection $enterdOptionIds
     */
    public function __construct(User $actor, Lottery $lottery, Collection $unenterdOptionIds, Collection $enterdOptionIds)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
        $this->unenterdOptionIds = $unenterdOptionIds;
        $this->enterdOptionIds = $enterdOptionIds;
    }
}
