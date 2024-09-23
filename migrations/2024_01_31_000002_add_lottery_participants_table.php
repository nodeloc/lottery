<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('lottery_participants', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('lottery_id');
    $table->unsignedTinyInteger('status')->comment('0 未中奖 1中奖');
    $table->unsignedInteger('user_id')->nullable();
    $table->timestamps();

    $table->index('lottery_id');
    $table->index('user_id');

    $table->foreign('lottery_id')
        ->references('id')
        ->on('lotteries')
        ->onDelete('cascade');

    $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('set null');
});
