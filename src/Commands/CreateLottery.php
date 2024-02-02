<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Commands;

use Flarum\Post\Post;
use Flarum\User\User;

class CreateLottery
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Post
     */
    public $post;

    /**
     * @var array
     */
    public $data;

    /**
     * @var callable
     */
    public $saveLotteryOn;

    /**
     * @param User          $actor
     * @param Post          $post
     * @param array         $data
     * @param callable|null $saveLotteryOn
     */
    public function __construct(User $actor, Post $post, array $data, callable $saveLotteryOn = null)
    {
        $this->actor = $actor;
        $this->post = $post;
        $this->data = $data;
        $this->saveLotteryOn = $saveLotteryOn ?: function (callable $callback) {
            return $callback();
        };
    }
}
