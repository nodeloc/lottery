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
use Nodeloc\Lottery\LotteryOption;

class LotteryOptionSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'lottery_options';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param LotteryOption $option
     *
     * @return array
     */
    protected function getDefaultAttributes($option)
    {
        $attributes = [
            'operator_type'      => $option->operator_type,
            'operator'    => $option->operator,
            'operator_value'    => $option->operator_value,
            'createdAt'   => $this->formatDate($option->created_at),
            'updatedAt'   => $this->formatDate($option->updated_at),
            'voteCount'   => $this->actor->can('seeLotteryCount', $option->lottery) ? (int) $option->vote_count : null,
        ];

        return $attributes;
    }
}
