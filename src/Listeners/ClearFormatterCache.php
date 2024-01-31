<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Listeners;

use Flarum\Settings\Event\Saved;

class ClearFormatterCache
{
    public function handle(Saved $event): void
    {
        foreach ($event->settings as $key => $value) {
            if ($key === 'nodeloc-lottery.optionsColorBlend') {
                resolve('nodeloc-user-bio.formatter')->flush();
                return;
            }
        }
    }
}
