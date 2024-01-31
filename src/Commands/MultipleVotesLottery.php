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

class MultipleVotesLottery
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
     * @var array
     */
    public $data;

    /**
     * @param User  $actor
     * @param int   $lotteryId
     * @param array $data
     */
    public function __construct(User $actor, int $lotteryId, array $data)
    {
        $this->actor = $actor;
        $this->lotteryId = $lotteryId;
        $this->data = $data;
    }
}
