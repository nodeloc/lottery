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

use Flarum\Database\AbstractModel;

/**
 * @property int            $id
 * @property string         $answer
 * @property string         $image_url
 * @property Lottery           $poll
 * @property int            $poll_id
 * @property int            $vote_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PollOption extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['answer', 'image_url'];

    /**
     * @param $answer
     *
     * @return static
     */
    public static function build($answer, $imageUrl = null)
    {
        $option = new static();

        $option->answer = $answer;
        $option->image_url = $imageUrl;

        return $option;
    }

    public function poll()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class, 'option_id');
    }

    public function refreshVoteCount(): self
    {
        $this->vote_count = $this->votes()->count();

        return $this;
    }
}