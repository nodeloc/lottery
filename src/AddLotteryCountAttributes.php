<?php

namespace Nodeloc\Lottery;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;

class AddLotteryCountAttributes
{
    public function __invoke(UserSerializer $serializer, User $user)
    {
        $attributes = [];
        $attributes['lotteryCount'] = $user->lottery_count;
        return $attributes;
    }
}
