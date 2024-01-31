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

class LotteryWasCreated
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
     * LotteryWasCreated constructor.
     *
     * @param User $actor
     * @param Lottery $lottery
     */
    public function __construct(User $actor, Lottery $lottery)
    {
        $this->actor = $actor;
        $this->lottery = $lottery;
    }
}
