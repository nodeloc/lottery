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

class LotteryParticipants extends AbstractModel
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

    protected $fillable = ['user_id'];

    /**
     * @param $lotteryId
     * @param $userId
     * @param $optionId
     *
     * @return static
     */
    public static function build($lotteryId, $userId)
    {
        $participants = new static();

        $participants->lottery_id = $lotteryId;
        $participants->user_id = $userId;

        return $participants;
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
