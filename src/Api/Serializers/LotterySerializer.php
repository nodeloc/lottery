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

class LotterySerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'lottery';

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
            'question'           => $lottery->question,
            'hasEnded'           => $lottery->hasEnded(),
            'allowMultipleVotes' => $lottery->allow_multiple_votes,
            'maxVotes'           => $lottery->max_votes,
            'endDate'            => $this->formatDate($lottery->end_date),
            'createdAt'          => $this->formatDate($lottery->created_at),
            'updatedAt'          => $this->formatDate($lottery->updated_at),
            'canVote'            => $this->actor->can('vote', $lottery),
            'canEdit'            => $canEdit,
            'canDelete'          => $this->actor->can('delete', $lottery),
            'canSeeVoters'       => $this->actor->can('seeVoters', $lottery),
            'canChangeVote'      => $this->actor->can('changeVote', $lottery),
        ];

        if ($this->actor->can('seeVoteCount', $lottery)) {
            $attributes['voteCount'] = (int) $lottery->vote_count;
        }

        if ($canEdit) {
            $attributes['publicPoll'] = $lottery->public_lottery;
            $attributes['hideVotes'] = $lottery->hide_votes;
            $attributes['allowChangeVote'] = $lottery->allow_change_vote;
        }

        return $attributes;
    }

    public function options($model)
    {
        return $this->hasMany(
            $model,
            LotteryOptionSerializer::class
        );
    }

    public function votes($model)
    {
        if ($this->actor->cannot('seeVoters', $model)) {
            return null;
        }

        return $this->hasMany(
            $model,
            LotteryVoteSerializer::class
        );
    }

    public function myVotes($model)
    {
        Lottery::setStateUser($this->actor);

        // When called inside ShowDiscussionController, Flarum has already pre-loaded our relationship incorrectly
        $model->unsetRelation('myVotes');

        return $this->hasMany(
            $model,
            LotteryVoteSerializer::class
        );
    }
}
