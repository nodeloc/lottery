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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * @property int    $id
 * @property string $question
 * @property-read bool             $public_lottery
 * @property-read bool             $allow_multiple_votes
 * @property-read int              $max_votes
 * @property-read bool             $hide_votes
 * @property-read bool             $allow_change_vote
 * @property int                   $vote_count
 * @property Post                  $post
 * @property User                  $user
 * @property int                   $post_id
 * @property int                   $user_id
 * @property \Carbon\Carbon|null   $end_date
 * @property \Carbon\Carbon        $created_at
 * @property \Carbon\Carbon        $updated_at
 * @property PollSettings          $settings
 * @property LotteryVote[]|Collection $votes
 * @property LotteryVote[]|Collection $myVotes
 *
 */
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

    /**
     * @param $question
     * @param $postId
     * @param $actorId
     * @param $endDate
     * @param $publicPoll
     *
     * @return static
     */
    public static function build($question, $postId, $actorId, $endDate, $publicPoll, $allowMultipleVotes = false, $maxVotes = 0, $hideVotes = false, $allowChangeVote = true)
    {
        $lottery = new static();

        $lottery->question = $question;
        $lottery->post_id = $postId;
        $lottery->user_id = $actorId;
        $lottery->end_date = $endDate;
        $lottery->settings = [
            'public_lottery'          => $publicPoll,
            'allow_multiple_votes' => $allowMultipleVotes,
            'max_votes'            => min(0, (int) $maxVotes),
            'hide_votes'           => $hideVotes,
            'allow_change_vote'    => $allowChangeVote,
        ];

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
    public function votes()
    {
        return $this->hasMany(LotteryVote::class);
    }

    public function refreshVoteCount(): self
    {
        $this->vote_count = $this->votes()->count();

        return $this;
    }

    protected static $stateUser;

    public function myVotes(User $user = null)
    {
        $user = $user ?: static::$stateUser;

        return $this->votes()->where('user_id', $user ? $user->id : null);
    }

    public static function setStateUser(User $user)
    {
        static::$stateUser = $user;
    }

    protected function getPublicPollAttribute()
    {
        return (bool) Arr::get($this->settings, 'public_lottery');
    }

    protected function getAllowMultipleVotesAttribute()
    {
        return (bool) Arr::get($this->settings, 'allow_multiple_votes');
    }

    protected function getMaxVotesAttribute()
    {
        return (int) Arr::get($this->settings, 'max_votes');
    }

    protected function getHideVotesAttribute(): bool
    {
        return (bool) Arr::get($this->settings, 'hide_votes');
    }

    protected function getAllowChangeVoteAttribute(): bool
    {
        return (bool) Arr::get($this->settings, 'allow_change_vote', true);
    }
}
