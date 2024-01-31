<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Console;

use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\LotteryOption;
use Illuminate\Console\Command;

class RefreshVoteCountCommand extends Command
{
    protected $signature = 'nodeloc:lottery:refresh';

    protected $description = 'Re-calculate the total number of votes per option';

    public function handle()
    {
        $progress = $this->output->createProgressBar(Lottery::query()->count() + LotteryOption::query()->count());

        Lottery::query()->each(function (Lottery $lottery) use ($progress) {
            $lottery->refreshVoteCount()->save();

            $progress->advance();
        });

        LotteryOption::query()->each(function (LotteryOption $option) use ($progress) {
            $option->refreshVoteCount()->save();

            $progress->advance();
        });

        $progress->finish();

        $this->info('Done.');
    }
}
