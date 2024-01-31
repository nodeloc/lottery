<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery;

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;

class PollRepository
{
    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return Lottery::query();
    }

    /**
     * @param User|null $user
     *
     * @return Builder<Lottery>
     */
    public function queryVisibleTo(?User $user = null): Builder
    {
        $query = $this->query();

        if ($user !== null) {
            $query->whereVisibleTo($user);
        }

        return $query;
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, User $actor = null): Lottery
    {
        return $this->queryVisibleTo($actor)->findOrFail($id);
    }
}
