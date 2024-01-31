<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Nodeloc\Lottery\LotteryVote;

class LotteryVoteSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'lottery_votes';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param LotteryVote $vote
     *
     * @return array
     */
    protected function getDefaultAttributes($vote)
    {
        return [
            'lotteryId'    => $vote->lottery_id,
            'optionId'  => $vote->option_id,
            'createdAt' => $this->formatDate($vote->created_at),
            'updatedAt' => $this->formatDate($vote->updated_at),
        ];
    }

    public function lottery($model)
    {
        return $this->hasOne(
            $model,
            LotterySerializer::class
        );
    }

    public function option($model)
    {
        return $this->hasOne(
            $model,
            LotteryOptionSerializer::class
        );
    }

    public function user($model)
    {
        return $this->hasOne(
            $model,
            BasicUserSerializer::class
        );
    }
}
