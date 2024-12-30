<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery;

use Flarum\Api\Controller;
use Flarum\Api\Serializer;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use Nodeloc\Lottery\Condition\LotteryAttendCondition;
use Nodeloc\Lottery\Condition\LotterySentCondition;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\Post\Post;
use Flarum\Settings\Event\Saved as SettingsSaved;
use Nodeloc\Lottery\Api\Controllers;
use Nodeloc\Lottery\Api\Serializers\LotterySerializer;
use Xypp\Collector\Extend\ConditionProvider;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),
    (new Extend\View())
        ->namespace('nodeloc-lottery', __DIR__.'/views'),
    (new Extend\Routes('api'))
        ->post('/nodeloc/lottery', 'nodeloc.lottery.create', Controllers\CreateLotteryController::class)
        ->get('/nodeloc/lottery/{id}', 'nodeloc.lottery.show', Controllers\ShowLotteryController::class)
        ->patch('/nodeloc/lottery/{id}', 'nodeloc.lottery.edit', Controllers\EditLotteryController::class)
        ->delete('/nodeloc/lottery/{id}', 'nodeloc.lottery.delete', Controllers\DeleteLotteryController::class)
        ->patch('/nodeloc/lottery/{id}/enter', 'nodeloc.lottery.enter', Controllers\EnterLotteryController::class),
    (new Extend\ApiSerializer(UserSerializer::class))
        ->attributes(AddLotteryCountAttributes::class),
    (new Extend\Model(Post::class))
        ->hasOne('lottery', Lottery::class, 'post_id', 'id'),

    (new Extend\Model(Discussion::class))
        ->hasOne('lottery', Lottery::class, 'post_id', 'first_post_id'),

    (new Extend\Event())
        ->listen(Saving::class,Listeners\SaveLotteryToDiscussion::class)
        ->listen(PostSaving::class, Listeners\SaveLotteryToDatabase::class)
        ->listen(SettingsSaved::class, Listeners\ClearFormatterCache::class),
    (new ConditionProvider)
        ->provide(LotterySentCondition::class)
        ->provide(LotteryAttendCondition::class),
    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(Api\AddDiscussionAttributes::class),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->hasOne('lottery', LotterySerializer::class)
        ->attributes(Api\AddPostAttributes::class),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(Api\AddForumAttributes::class),

    (new Extend\ApiController(Controller\ListDiscussionsController::class))
        ->addOptionalInclude(['firstPost.lottery']),

    (new Extend\ApiController(Controller\ShowDiscussionController::class))
        ->addInclude(['posts.lottery', 'posts.lottery.options', 'posts.lottery.lottery_participants'])
        ->addOptionalInclude(['posts.lottery.participants', 'posts.lottery.participants.user']),

    (new Extend\ApiController(Controller\CreateDiscussionController::class))
        ->addInclude(['firstPost.lottery', 'firstPost.lottery.options', 'firstPost.lottery.lottery_participants'])
        ->addOptionalInclude(['firstPost.lottery.participants', 'firstPost.lottery.participants.user']),

    (new Extend\ApiController(Controller\CreatePostController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.participants', 'lottery.participants.user'])
        ->addOptionalInclude(['lottery.participants', 'lottery.participants.user']),

    (new Extend\ApiController(Controller\ListPostsController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.participants', 'lottery.participants.user', 'lottery.lottery_participants'])
        ->addOptionalInclude(['lottery.participants', 'lottery.participants.user']),

    (new Extend\ApiController(Controller\ShowPostController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.participants', 'lottery.participants.user', 'lottery.lottery_participants'])
        ->addOptionalInclude(['lottery.participants', 'lottery.participants.user']),


    (new Extend\Console())
        ->command(Console\RefreshParticipantsCountCommand::class)
        ->command(Console\DrawCommand::class)
        ->schedule(Console\DrawCommand::class,Console\DrawSchedule::class),

    (new Extend\Policy())
        ->modelPolicy(Lottery::class, Access\LotteryPolicy::class)
        ->modelPolicy(Post::class, Access\PostPolicy::class),

    (new Extend\Settings())
        ->default('nodeloc-lottery.maxOptions', 10)
        ->default('nodeloc-lottery.optionsColorBlend', true)
        ->serializeToForum('allowLotteryOptionImage', 'nodeloc-lottery.allowOptionImage', 'boolval')
        ->serializeToForum('lotteryMaxOptions', 'nodeloc-lottery.maxOptions', 'intval')
        ->registerLessConfigVar('nodeloc-lottery-options-color-blend', 'nodeloc-lottery.optionsColorBlend', function ($value) {
            return $value ? 'true' : 'false';
        }),
    (new Extend\Notification())
    ->type(Notification\DrawLotteryBlueprint::class, Serializer\BasicDiscussionSerializer::class, ['alert', 'email'])
    ->type(Notification\FailLotteryBlueprint::class, Serializer\BasicDiscussionSerializer::class, ['alert', 'email'])
    ->type(Notification\FinishLotteryBlueprint::class, Serializer\BasicDiscussionSerializer::class, ['alert', 'email']),
    (new Extend\ModelVisibility(Lottery::class))
        ->scope(Access\ScopeLotteryVisibility::class),
];
