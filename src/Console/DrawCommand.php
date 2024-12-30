<?php

namespace Nodeloc\Lottery\Console;

use Carbon\Carbon;
use Exception;
use Flarum\Discussion\Discussion;
use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\Notification\DrawLotteryBlueprint;
use UnexpectedValueException;
use Flarum\Notification\NotificationSyncer;
use Mattoid\MoneyHistory\model\UserMoneyHistory;
use Nodeloc\Lottery\Notification\FailLotteryBlueprint;
use Nodeloc\Lottery\Notification\FinishLotteryBlueprint;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xypp\Collector\Data\ConditionData;
use Xypp\Collector\Event\UpdateCondition;
class DrawCommand extends Command
{
    protected $signature = 'nodeloc:lottery:draw';
    protected $description = 'Draw lottery by date.';
    protected $events;
    protected $prefix = 'Lottery #';
    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var NotificationSyncer
     */
    private $notifications;
    protected $translator;
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events,  NotificationSyncer $notifications, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->notifications = $notifications;
        $this->events = $events;
        $this->translator = $translator;
        $this->notifications = $notifications;
    }
    public function handle()
    {
        // 查询所有状态为0的抽奖帖
        $lotteries = Lottery::where('status', 0)->get();
        foreach ($lotteries as $lottery) {
            // 在事务中处理每一条抽奖
            // 检查 $lottery->end_date 是否为 null
            if ($lottery->end_date === null) {
                $this->info($lottery->id . ' has null end_date.');
                continue; // 跳过这个抽奖
            }
            // 检查结束时间是否已经大于当前时间
            $currentTime = Carbon::now();
            $participantsCount = $lottery->participants()->count();
            if ($lottery->end_date->lt($currentTime) || $participantsCount >= $lottery->max_participants) {
                $minParticipants = $lottery->min_participants;
                if ($participantsCount >= $minParticipants) {
                    // 计算参与金额总数
                    $totalEntranceFee = $participantsCount * $lottery->price;

                    /*
                    $source = 'LOTTERY_FEE';
                    $sourceDesc = $this->translator->trans("antoinefr-money.forum.history.lottery-fee");
                    // 扣除参与金额
                    $participants = $lottery->participants()->get();
                    foreach ($participants as $participant) {
                        $participant->user->decrement('money', $lottery->price);
                        $money =-$lottery->price;
                        if ($money > 0 || $money < 0) {
                            $userMoneyHistory = new UserMoneyHistory();
                            $userMoneyHistory->user_id = $participant->user->id;
                            $userMoneyHistory->type = $money > 0 ? "C" : "D";
                            $userMoneyHistory->money = $money > 0 ? $money : -$money;
                            $userMoneyHistory->source = $source;
                            $userMoneyHistory->source_desc = $sourceDesc;
                            $userMoneyHistory->balance_money = isset($participant->user->init_money) ?$participant->user->init_money : $participant->user->money - $money;
                            $userMoneyHistory->last_money = $participant->user->money;
                            $userMoneyHistory->create_user_id = isset($participant->user->create_user_id) ? $participant->user->create_user_id : $participant->user->id;
                            $userMoneyHistory->change_time = Date("Y-m-d H:i:s");
                            $userMoneyHistory->save();
                        }
                    }*/

                    // 人数达到了，将抽奖帖状态设置为1 (status = 1)
                    $lottery->update(['status' => 1]);

                    // 更新中奖用户的状态
                    $winners = $lottery->participants()
                        ->inRandomOrder()
                        ->limit($lottery->amount)
                        ->get();
                    $winnerIds = $winners->pluck('id');
                    $lottery->participants()->whereIn('id', $winnerIds)->update(['status' => 1]);
                    // 给抽奖发起者加上参与金额
                    $lottery->user->increment('money', $totalEntranceFee);
                    // 增加抽奖次数
                    $lottery->user->increment('lottery_count', 1);
                    // 增加小鱼的统计数据
                    $this->events->dispatch(
                        new UpdateCondition(
                            $lottery->user,
                            [new ConditionData('lottery_sent', 1)]
                        )
                    );


                    $source = 'LOTTERY_IN';
                    $sourceDesc = $this->translator->trans("antoinefr-money.forum.history.lottery-in");
                    $money =$totalEntranceFee;
                    if ($money > 0 || $money < 0) {
                        $userMoneyHistory = new UserMoneyHistory();
                        $userMoneyHistory->user_id = $lottery->user->id;
                        $userMoneyHistory->type = $money > 0 ? "C" : "D";
                        $userMoneyHistory->money = $money > 0 ? $money : -$money;
                        $userMoneyHistory->source = $source;
                        $userMoneyHistory->source_desc = $sourceDesc;
                        $userMoneyHistory->balance_money = isset($lottery->user->init_money) ?$lottery->user->init_money : $lottery->user->money - $money;
                        $userMoneyHistory->last_money = $lottery->user->money;
                        $userMoneyHistory->create_user_id = isset($lottery->user->create_user_id) ? $lottery->user->create_user_id : $lottery->user->id;
                        $userMoneyHistory->change_time = Date("Y-m-d H:i:s");
                        $userMoneyHistory->save();
                    }

                    $d = Discussion::where('first_post_id', $lottery->post_id)->first();

                    //通知发布抽奖帖用户
                    $this->notifications->sync(new FinishLotteryBlueprint($d),[$d->user]);

                    // Send notifications to other participants of the discussion
                    $recipientsBuilder = User::whereIn('id',$winners->pluck('user_id'));
                    $recipients = $recipientsBuilder
                        ->get();
                    $this->notifications->sync( new DrawLotteryBlueprint($d,$d->user), $recipients->all());

                    $this->info($lottery->id . ' drawn successfully.');
                } else {
                    // 人数不足，将抽奖状态设为2 (status = 2)
                    $lottery->update(['status' => 2]);
                    //所有参与用户返还金钱
                    $source = 'LOTTERY_FEE_RETURN';
                    $sourceDesc = $this->translator->trans("antoinefr-money.forum.history.lottery-fee-return");
                    $participants = $lottery->participants()->get();
                    foreach ($participants as $participant) {
                        $participant->user->increment('money', $lottery->price);
                        $money =$lottery->price;
                        if ($money > 0 || $money < 0) {
                            $userMoneyHistory = new UserMoneyHistory();
                            $userMoneyHistory->user_id = $participant->user->id;
                            $userMoneyHistory->type = $money > 0 ? "C" : "D";
                            $userMoneyHistory->money = $money > 0 ? $money : -$money;
                            $userMoneyHistory->source = $source;
                            $userMoneyHistory->source_desc = $sourceDesc;
                            $userMoneyHistory->balance_money = isset($participant->user->init_money) ?$participant->user->init_money : $participant->user->money - $money;
                            $userMoneyHistory->last_money = $participant->user->money;
                            $userMoneyHistory->create_user_id = isset($participant->user->create_user_id) ? $participant->user->create_user_id : $participant->user->id;
                            $userMoneyHistory->change_time = Date("Y-m-d H:i:s");
                            $userMoneyHistory->save();
                        }
                    }
                    $d = Discussion::where('first_post_id', $lottery->post_id)->first();
                    $this->notifications->sync(new FailLotteryBlueprint($d),[$d->user]);
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
