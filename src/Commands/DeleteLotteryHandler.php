<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Commands;

use Nodeloc\Lottery\PollRepository;

class DeletePollHandler
{
    /**
     * @var PollRepository
     */
    protected $polls;

    public function __construct(PollRepository $polls)
    {
        $this->polls = $polls;
    }

    public function handle(DeletePoll $command)
    {
        $poll = $this->polls->findOrFail($command->pollId, $command->actor);

        $command->actor->assertCan('delete', $poll);

        $poll->delete();
    }
}
