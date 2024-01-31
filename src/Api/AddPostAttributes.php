<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Api;

class AddPostAttributes
{
    public function __invoke($serializer, $post, $attributes)
    {
        $attributes['canStartLottery'] = $serializer->getActor()->can('startPoll', $post);

        return $attributes;
    }
}
