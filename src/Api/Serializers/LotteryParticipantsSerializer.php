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
use Nodeloc\Lottery\LotteryParticipants;

class LotteryParticipantsSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'lottery_participants';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param LotteryParticipants $participants
     *
     * @return array
     */
    protected function getDefaultAttributes($participants)
    {
        return [
            'lotteryId'    => $participants->lottery_id,
            'status'      =>$participants->status,
            'createdAt' => $this->formatDate($participants->created_at),
            'updatedAt' => $this->formatDate($participants->updated_at),
        ];
    }

    public function lottery($model)
    {
        return $this->hasOne(
            $model,
            LotterySerializer::class
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
