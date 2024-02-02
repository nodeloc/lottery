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

use Flarum\Database\AbstractModel;
use Flarum\Database\ScopeVisibilityTrait;
use Flarum\Post\Post;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Arr;

class Lottery extends AbstractModel
{
    use ScopeVisibilityTrait;

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    protected $casts = [
        'settings'   => AsArrayObject::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'end_date'   => 'datetime',
    ];
    public $fillable = [
        'enter_count',
    ];
    public static function build($prizes, $postId, $actorId, $endDate, $price, $amount, $min_participants=0, $max_participants=999999,$can_cancel_enter = false)
    {
        $lottery = new static();

        $lottery->prizes = $prizes;
        $lottery->price = $price;
        $lottery->amount = $amount;
        $lottery->post_id = $postId;
        $lottery->user_id = $actorId;
        $lottery->end_date = $endDate;

        $lottery->min_participants = $min_participants;
        $lottery->max_participants = $max_participants;
        $lottery->can_cancel_enter= $can_cancel_enter;

        return $lottery;
    }

    /**
     * @return bool
     */
    public function hasEnded()
    {
        return $this->end_date !== null && $this->end_date->isPast();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(LotteryOption::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function participants()
    {
        return $this->hasMany(LotteryParticipants::class);
    }

    public function refreshParticipantsCount(): self
    {
        $this->participants_count = $this->participants()->count();

        return $this;
    }

    protected static $stateUser;

    public function lottery_participants(User $user = null)
    {
        $user = $user ?: static::$stateUser;

        return $this->participants()->where('user_id', $user ? $user->id : null);
    }

    public static function setStateUser(User $user)
    {
        static::$stateUser = $user;
    }
}
