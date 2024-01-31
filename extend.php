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
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Post\Event\Saving as PostSaving;
use Flarum\Post\Post;
use Flarum\Settings\Event\Saved as SettingsSaved;
use Nodeloc\Lottery\Api\Controllers;
use Nodeloc\Lottery\Api\Serializers\LotterySerializer;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Routes('api'))
        ->post('/nodeloc/lottery', 'nodeloc.lottery.create', Controllers\CreateLotteryController::class)
        ->get('/nodeloc/lottery/{id}', 'nodeloc.lottery.show', Controllers\ShowLotteryController::class)
        ->patch('/nodeloc/lottery/{id}', 'nodeloc.lottery.edit', Controllers\EditLotteryController::class)
        ->delete('/nodeloc/lottery/{id}', 'nodeloc.lottery.delete', Controllers\DeleteLotteryController::class)
        ->patch('/nodeloc/lottery/{id}/votes', 'nodeloc.lottery.votes', Controllers\MultipleVotesLotteryController::class),

    (new Extend\Model(Post::class))
        ->hasMany('lottery', Lottery::class, 'post_id', 'id'),

    (new Extend\Model(Discussion::class))
        ->hasMany('lottery', Lottery::class, 'post_id', 'first_post_id'),

    (new Extend\Event())
        ->listen(PostSaving::class, Listeners\SaveLotteryToDatabase::class)
        ->listen(SettingsSaved::class, Listeners\ClearFormatterCache::class),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attributes(Api\AddDiscussionAttributes::class),

    (new Extend\ApiSerializer(PostSerializer::class))
        ->hasMany('lottery', LotterySerializer::class)
        ->attributes(Api\AddPostAttributes::class),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(Api\AddForumAttributes::class),

    (new Extend\ApiController(Controller\ListDiscussionsController::class))
        ->addOptionalInclude(['firstPost.lottery']),

    (new Extend\ApiController(Controller\ShowDiscussionController::class))
        ->addInclude(['posts.lottery', 'posts.lottery.options', 'posts.lottery.myVotes', 'posts.lottery.myVotes.option'])
        ->addOptionalInclude(['posts.lottery.votes', 'posts.lottery.votes.user', 'posts.lottery.votes.option']),

    (new Extend\ApiController(Controller\CreateDiscussionController::class))
        ->addInclude(['firstPost.lottery', 'firstPost.lottery.options', 'firstPost.lottery.myVotes', 'firstPost.lottery.myVotes.option'])
        ->addOptionalInclude(['firstPost.lottery.votes', 'firstPost.lottery.votes.user', 'firstPost.lottery.votes.option']),

    (new Extend\ApiController(Controller\CreatePostController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.myVotes', 'lottery.myVotes.option'])
        ->addOptionalInclude(['lottery.votes', 'lottery.votes.user', 'lottery.votes.option']),

    (new Extend\ApiController(Controller\ListPostsController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.myVotes', 'lottery.myVotes.option'])
        ->addOptionalInclude(['lottery.votes', 'lottery.votes.user', 'lottery.votes.option']),

    (new Extend\ApiController(Controller\ShowPostController::class))
        ->addInclude(['lottery', 'lottery.options', 'lottery.myVotes', 'lottery.myVotes.option'])
        ->addOptionalInclude(['lottery.votes', 'lottery.votes.user', 'lottery.votes.option']),

    (new Extend\Console())
        ->command(Console\RefreshVoteCountCommand::class),

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

    (new Extend\ModelVisibility(Lottery::class))
        ->scope(Access\ScopeLotteryVisibility::class),
];
