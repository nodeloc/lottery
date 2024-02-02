<?php

namespace Nodeloc\Lottery\Console;

use Carbon\Carbon;
use Exception;
use Flarum\Discussion\Discussion;
use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\Notification\DrawLotteryBlueprint;
use UnexpectedValueException;
use Flarum\Notification\NotificationSyncer;


class DrawCommand extends Command
{
    protected $signature = 'nodeloc:lottery:draw';
    protected $description = 'Draw lottery by date.';

    protected $prefix = 'Lottery #';
    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var NotificationSyncer
     */
    private $notifications;

    public function __construct(SettingsRepositoryInterface $settings, NotificationSyncer $notifications)
    {
        parent::__construct();

        $this->settings = $settings;
        $this->notifications = $notifications;
    }
    public function handle()
    {
        echo( ' Start draw.');
        // 查询所有状态为0的抽奖帖
        $lotteries = Lottery::where('status', 0)->get();
        foreach ($lotteries as $lottery) {
            // 在事务中处理每一条抽奖
                // 检查结束时间是否已经大于当前时间
                $currentTime = Carbon::now();
                if ($lottery->end_date->lt($currentTime)) {
                    // 查询参与人数是否大于最少要求人数
                    $participantsCount = $lottery->participants()->count();
                    $minParticipants = $lottery->min_participants;

                    if ($participantsCount >= $minParticipants) {
                        // 人数达到了，将抽奖帖状态设置为1 (status = 1)
                        $lottery->update(['status' => 1]);

                        // 更新中奖用户的状态
                        $winners = $lottery->participants()
                            ->inRandomOrder()
                            ->limit($lottery->amount)
                            ->get();

                        $winnerIds = $winners->pluck('id');
                        $lottery->participants()->whereIn('id', $winnerIds)->update(['status' => 1]);
                        $d = Discussion::where('first_post_id', $lottery->post_id)->first();
                        //通知发布抽奖帖用户
                        $this->notifications->sync(new DrawLotteryBlueprint($d),[$d->user]);
                        $this->notifications->sync( new DrawLotteryBlueprint($d), [$winnerIds]);

                        $this->info($lottery->id . ' drawn successfully.');
                    } else {
                        // 人数不足，将抽奖状态设为2 (status = 2)
                        $lottery->update(['status' => 2]);
                        $d = Discussion::where('first_post_id', $lottery->post_id)->first();
                        $this->notifications->sync(new DrawLotteryBlueprint($d),[$d->user]);
                        $this->info( $lottery->id . ' canceled due to insufficient participants.');
                    }
                }
        }

        $this->info('Done.');
    }

    public function info($string, $verbosity = null): void
    {
        parent::info($this->prefix . ' | ' . $string, $verbosity);
    }

}
