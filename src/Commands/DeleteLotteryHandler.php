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

use Nodeloc\Lottery\LotteryRepository;

class DeleteLotteryHandler
{
    /**
     * @var LotteryRepository
     */
    protected $lottery;

    public function __construct(LotteryRepository $lottery)
    {
        $this->lottery = $lottery;
    }

    public function handle(DeleteLottery $command)
    {
        $lottery = $this->lottery->findOrFail($command->lotteryId, $command->actor);

        $command->actor->assertCan('delete', $lottery);

        $lottery->delete();
    }
}
