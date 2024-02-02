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
use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;

class ScopeLotteryVisibility
{
    public function __invoke(User $actor, Builder $query)
    {
        $query->whereExists(function ($query) use ($actor) {
            $query->selectRaw('1')
                 ->from('posts')
                 ->whereColumn('posts.id', 'lotteries.post_id');
            Post::query()->setQuery($query)->whereVisibleTo($actor);
        });
    }
}
