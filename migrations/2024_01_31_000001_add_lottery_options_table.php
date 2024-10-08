<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('lottery_options')) {
            return;
        }

        $schema->create('lottery_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('operator_type', 256)->comment('运算类型');
            $table->unsignedInteger('lottery_id');
            $table->unsignedTinyInteger('operator')->comment('0 等于 1 小于等于 2 大于等于');
            $table->integer('operator_value')->comment('数值');
            $table->timestamps();

            $table->index('lottery_id');

            $table->foreign('lottery_id')
                ->references('id')
                ->on('lotteries')
                ->onDelete('cascade');
        });

    },
    'down' => function (Builder $schema) {

    },
];
