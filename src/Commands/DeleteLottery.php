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

use Flarum\User\User;

class DeleteLottery
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $lotteryId;

    /**
     * @param User $actor
     * @param int  $lotteryId
     */
    public function __construct(User $actor, int $lotteryId)
    {
        $this->actor = $actor;
        $this->lotteryId = $lotteryId;
    }
}
