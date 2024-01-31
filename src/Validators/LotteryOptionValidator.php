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
            'answer'   => ['required', 'string', 'max:255'],
            'imageUrl' => ['nullable', 'url', 'max:255'],
        ];
    }
}
