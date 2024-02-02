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

return Migration::createTable('lottery_options', function (Blueprint $table) {
    $table->increments('id');

    $table->string('answer');

    $table->integer('lottery_id')->unsigned();
    $table->integer('vote_count')->unsigned();
    $table->string('image_url');

    $table->timestamps();

    $table->foreign('lottery_id')->references('id')->on('lotteries')->onDelete('cascade');
});
