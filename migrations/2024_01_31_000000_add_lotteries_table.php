<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('lotteries')) {
            return;
        }

        $schema->create('lotteries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prizes')->comment('奖品');
            $table->integer('price')->nullable()->comment('参与价格');
            $table->integer('amount')->comment('数量');
            $table->timestamp('end_date')->nullable()->comment('开奖日期');
            $table->integer('post_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('min_participants')->unsigned()->comment('最少人数');
            $table->integer('max_participants')->unsigned()->comment('最多人数');
            $table->integer('enter_count')->default(0);
            $table->boolean('can_cancel_enter')->nullable();
            $table->tinyInteger('status')->comment('0:已发布 1:已开奖 2:到期人数不够取消');
            $table->json('settings');
            $table->timestamps();

            $table->primary('id');
            $table->index('user_id');
            $table->index('post_id');

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

    },
    'down' => function (Builder $schema) {

    },
];

