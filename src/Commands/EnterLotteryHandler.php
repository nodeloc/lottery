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

use Flarum\Foundation\ErrorHandling\Reporter;
use Flarum\Foundation\ValidationException;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Nodeloc\Lottery\Events\LotteryCancelEnter;
use Nodeloc\Lottery\Events\LotteryWasEntered;
use Nodeloc\Lottery\Lottery;
use Nodeloc\Lottery\LotteryRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Pusher;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnterLotteryHandler
{
    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Factory
     */
    private $validation;

    /**
     * @var ConnectionResolverInterface
     */
    private $db;

    /**
     * @var LotteryRepository
     */
    private $lottery;

    /**
     * @param Dispatcher $events
     * @param SettingsRepositoryInterface $settings
     * @param Container $container
     */
    public function __construct(LotteryRepository $lottery, Dispatcher $events, SettingsRepositoryInterface $settings, Container $container, Factory $validation, ConnectionResolverInterface $db)
    {
        $this->lottery = $lottery;
        $this->events = $events;
        $this->settings = $settings;
        $this->container = $container;
        $this->validation = $validation;
        $this->translator = resolve(TranslatorInterface::class);
        $this->db = $db;
    }

    /**
     * @throws PermissionDeniedException
     * @throws ValidationException
     */
    public function handle(EnterLottery $command)
    {
        $actor = $command->actor;

        $lottery = $this->lottery->findOrFail($command->lotteryId, $actor);

        $actor->assertCan('enter', $lottery);

        //已参与抽奖
        $existingParticipant = $lottery->participants()
            ->where('user_id', $actor->id)
            ->first();
        if ($existingParticipant) {
            throw new ValidationException([
                'lottery' => $this->translator->trans('nodeloc-lottery.forum.composer_discussion.in_queue_alert'),
            ]);
        }

        // 检查用户是否在对应主题回帖
        $discussionId = $lottery->post->discussion_id;
        $userPostedInDiscussion = $actor->posts()
            ->where('discussion_id', $discussionId)
            ->exists();

        if (!$userPostedInDiscussion) {
            throw new ValidationException([
                'lottery' => $this->translator->trans('nodeloc-lottery.forum.composer_discussion.no_post_in_discussion_alert'),
            ]);
        }

        if($lottery->getAttribute('enter_count')>$lottery->getAttribute('max_participants')){
            throw new ValidationException([
                'lottery' => $this->translator->trans('nodeloc-lottery.forum.too_many_participants'),
            ]);
        }

        // 检查用户是否满足条件
        if (!$this->userMeetsConditions($actor, $lottery)) {
            throw new ValidationException([
                'lottery' => $this->translator->trans('nodeloc-lottery.forum.composer_discussion.no_permission_alert'),
            ]);
        }

        if($lottery->getAttribute('price') > $actor->getAttribute('money')){
            throw new ValidationException([
                'lottery' => $this->translator->trans("nodeloc-lottery.forum.modal.not_enough").' '.$this->translator->trans("nodeloc-lottery.forum.modal.money"),
            ]);
        }

        $this->db->transaction(function () use ($lottery, $actor) {
            // 减少用户的 money
            // 报名不扣钱
            //$actor->decrement('money', $lottery->getAttribute('price'));
            $participants = $lottery->participants()->create([
                'user_id' => $actor->id,
            ]);
            // Legacy event for backward compatibility with single-enter lottery. Can be removed in breaking release.
            $this->events->dispatch(new LotteryWasEntered($actor, $lottery, $participants));
            // 增加 $lottery 的 enter_count
            $lottery->increment('enter_count');
        });

        return $lottery;
    }
    // 辅助方法，用于检查用户是否满足条件
    protected function userMeetsConditions(User $user, $lottery)
    {
        $conditions = $lottery->options;
        foreach ($conditions as $condition) {
            $this->checkCondition($user, $condition);
        }
        return true;
    }

    protected function checkCondition(User $user, $condition)
    {
        $value = $this->getConditionValue($user, $condition['operator_type']);
        $operator = $condition['operator'];
        $threshold = $condition['operator_value'];

        if (!$this->meetsCondition($value, $operator, $threshold)) {
            $errorMessageKey = "nodeloc-lottery.forum.modal.{$condition['operator_type']}";
            throw new ValidationException([
                'lottery' => $this->translator->trans("nodeloc-lottery.forum.modal.not_enough").' '.$this->translator->trans($errorMessageKey),
            ]);
        }
    }

    protected function getConditionValue(User $user, $operatorType)
    {
        switch ($operatorType) {
            case 'discussions_started':
                return $user->discussions()->where('is_private', false)
                    ->count();
            case 'posts_made':
                return $user->posts()
                    ->where('type', 'comment')
                    ->where('is_private', false)
                    ->count();
            case 'money':
                return $user->getAttribute('money');
            case 'lotteries_made':
                return Lottery::where('user_id', $user->id)->count();
            default:
                return 0; // 默认情况下为零
        }
    }

    protected function meetsCondition($count, $operator, $value)
    {
        switch ($operator) {
            case '0':
                return $count <= $value;
            case '1':
                return $count >= $value;
            default:
                return true; // 默认情况下条件是满足的
        }
    }
}
