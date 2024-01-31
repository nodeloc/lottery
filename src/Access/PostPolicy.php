<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Access;

use Flarum\Post\Post;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class PostPolicy extends AbstractPolicy
{
    public static $ALLOWED_POST_TYPES = ['comment'];

    public function startPoll(User $actor, Post $post)
    {
        if (!in_array($post->type, static::$ALLOWED_POST_TYPES)) {
            return $this->deny();
        }

        // Only allow lottery to be started if the user can start lottery in the discussion
        // and if the user can either edit the post or is currently creating a new post.
        // For example, actors cannot 'edit' a post they're currently creating if post editing is allowed until next reply.
        if ($actor->can('lottery.start', $post->discussion) && (!$post->exists || $actor->can('edit', $post))) {
            return $this->allow();
        }
    }
}
