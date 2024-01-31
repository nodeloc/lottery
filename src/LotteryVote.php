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
use Flarum\User\User;

/**
 * @property Lottery           $lottery
 * @property LotteryOption     $option
 * @property User           $user
 * @property int            $lottery_id
 * @property int            $option_id
 * @property int            $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LotteryVote extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['user_id', 'option_id'];

    /**
     * @param $lotteryId
     * @param $userId
     * @param $optionId
     *
     * @return static
     */
    public static function build($lotteryId, $userId, $optionId)
    {
        $vote = new static();

        $vote->lottery_id = $lotteryId;
        $vote->user_id = $userId;
        $vote->option_id = $optionId;

        return $vote;
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function option()
    {
        return $this->belongsTo(LotteryOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
