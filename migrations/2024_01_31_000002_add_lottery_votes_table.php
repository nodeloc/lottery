<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('lottery_votes', function (Blueprint $table) {
    $table->increments('id');

    $table->integer('lottery_id')->unsigned();
    $table->integer('option_id')->unsigned();
    $table->integer('user_id')->unsigned()->nullable();

    $table->timestamps();

    $table->foreign('lottery_id')->references('id')->on('lotteries')->onDelete('cascade');
    $table->foreign('option_id')->references('id')->on('lottery_options')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
});
