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

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use Nodeloc\Lottery\Lottery;

class LotteryPolicy extends AbstractPolicy
{

    public function seeParticipants(User $actor, Lottery $lottery)
    {
        if (!$actor->hasPermission('lottery.seeParticipants')) {
            return $this->deny();
        }

        if ($lottery->public_lottery) {
            return $this->allow();
        }
    }

    public function view(User $actor, Lottery $lottery)
    {
        if ($actor->can('view', $lottery->post)) {
            return $this->allow();
        }
    }

    public function enter(User $actor, Lottery $lottery)
    {
        if ($actor->can('lottery.enter', $lottery->post->discussion) && !$lottery->hasEnded()) {
            return $this->allow();
        }
    }

    public function cancelEnter(User $actor, Lottery $lottery)
    {
        if ($lottery->allow_cancel_enter && $actor->hasPermission('lottery.cancelEnter')) {
            return $this->allow();
        }
    }

    public function edit(User $actor, Lottery $lottery)
    {
        if ($actor->can('lottery.moderate', $lottery->post->discussion)) {
            return $this->allow();
        }

        if (!$lottery->hasEnded() && $actor->can('edit', $lottery->post)) {
            // User either created lottery & can edit own lottery or can edit all lottery in post
            if (($actor->id === $lottery->user_id && $actor->hasPermission('lottery.selfEdit'))
                || ($actor->id == $lottery->post->user_id && $actor->hasPermission('lottery.selfPostEdit'))) {
                return $this->allow();
            }
        }
    }

    public function delete(User $actor, Lottery $lottery)
    {
        return $this->edit($actor, $lottery);
    }
}
