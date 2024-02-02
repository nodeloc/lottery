<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery;

use Flarum\Database\AbstractModel;

class LotteryOption extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = ['operator_type', 'operator', 'operator_value'];

    /**
     * @param $type
     * @param $operator
     * @param $value
     * @return static
     */
    public static function build($operator_type, $operator ,$operator_value)
    {
        $option = new static();

        $option->operator_type = $operator_type;
        $option->operator = $operator;
        $option->operator_value = $operator_value;

        return $option;
    }

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

}
