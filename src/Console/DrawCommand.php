<?php

namespace Nodeloc\Lottery\Console;

use Carbon\Carbon;
use Exception;
use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nodeloc\Lottery\Lottery;
use UnexpectedValueException;

class DrawCommand extends Command
{
    protected $signature = 'nodeloc:lottery:draw';
    protected $description = 'Draw lottery by date.';

    protected $prefix = 'Lottery #';

    public function handle()
    {
        // 查询所有状态为0的抽奖帖
        $lotteries = Lottery::where('status', 0)->get();

        foreach ($lotteries as $lottery) {
            // 在事务中处理每一条抽奖
            DB::transaction(function () use ($lottery) {
                // 检查结束时间是否已经大于当前时间
                $currentTime = Carbon::now();

                if ($lottery->end_date->gt($currentTime)) {
                    // 查询参与人数是否大于最少要求人数
                    $participantsCount = $lottery->participants()->count();
                    $minParticipants = $lottery->min_participants;

                    if ($participantsCount >= $minParticipants) {
                        // 人数达到了，将抽奖帖状态设置为1 (status = 1)
                        $lottery->update(['status' => 1]);

                        // 从参与者表中随机取 amount 个数
                        $winners = $lottery->participants()->inRandomOrder()->take($lottery->amount)->get();

                        // 将随机选中的用户的 status 设置为1
                        foreach ($winners as $winner) {
                            $winner->update(['status' => 1]);
                        }

                        $this->info($lottery->id . ' drawn successfully.');
                    } else {
                        // 人数不足，将抽奖状态设为2 (status = 2)
                        $lottery->update(['status' => 2]);
                        $this->info( $lottery->id . ' canceled due to insufficient participants.');
                    }
                }
            });
        }

        $this->info('Done.');
    }

    public function info($string, $verbosity = null): void
    {
        parent::info($this->prefix . ' | ' . $string, $verbosity);
    }

}
