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

return Migration::createTable('lottery', function (Blueprint $table) {
    $table->increments('id');

    $table->string('question');

    $table->integer('discussion_id')->unsigned();
    $table->integer('user_id')->unsigned()->nullable();
    $table->integer('vote_count')->unsigned();
    $table->integer('max_votes')->unsigned();

    $table->boolean('allow_multiple_votes');


    $table->boolean('public_lottery');
    $table->json('settings');
    $table->timestamp('end_date')->nullable();
    $table->timestamps();

    $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
});
