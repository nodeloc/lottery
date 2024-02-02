<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Validators;

use Flarum\Foundation\AbstractValidator;

class LotteryOptionValidator extends AbstractValidator
{
    protected function getRules()
    {
        return [
            'operator_type'   => ['required', 'string', 'max:256'],
            'operator' => ['required', 'integer', 'max:1'],
            'operator_value' => ['required', 'integer', 'max:999999'],
        ];
    }
}
