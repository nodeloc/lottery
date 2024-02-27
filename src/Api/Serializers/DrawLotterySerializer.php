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
use Nodeloc\Lottery\Lottery;

class DrawLotterySerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'drawLottery';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param Lottery $lottery
     *
     * @return array
     */
    protected function getDefaultAttributes($lottery)
    {
        $canEdit = $this->actor->can('edit', $lottery);

        $attributes = [
            'prizes' => $lottery->prizes,
            'price' => $lottery->price,
            'amount' => $lottery->amount,
            'hasEnded' => $lottery->hasEnded(),
            'min_participants' => $lottery->min_participants,
            'max_participants' => $lottery->max_participants,
            'enter_count' => $lottery->enter_count,
            'status' => $lottery->status,
            'endDate' => $this->formatDate($lottery->end_date),
            'createdAt' => $this->formatDate($lottery->created_at),
            'updatedAt' => $this->formatDate($lottery->updated_at),
            'canEnter' => $this->actor->can('enter', $lottery),
            'canEdit' => $canEdit,
            'canDelete' => $this->actor->can('delete', $lottery),
            'canCancelEnter' => $this->actor->can('cancelEnter', $lottery),
            'canSeeParticipants'       => $this->actor->hasPermission('lottery.seeParticipants'),
        ];

        return $attributes;
    }

    public function options($model)
    {
        return $this->hasMany(
            $model,
            LotteryOptionSerializer::class
        );
    }

    public function participants($model)
    {
        if (!$this->actor->hasPermission('lottery.seeParticipants')) {
            return null;
        }

        return $this->hasMany(
            $model,
            LotteryParticipantsSerializer::class
        );
    }

    public function lottery_participants($model)
    {
        Lottery::setStateUser($this->actor);

        // When called inside ShowDiscussionController, Flarum has already pre-loaded our relationship incorrectly
        $model->unsetRelation('lottery_participants');

        return $this->hasMany(
            $model,
            LotteryParticipantsSerializer::class
        );
    }
}
