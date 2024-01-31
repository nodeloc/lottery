<?php

/*
 * This file is part of nodeloc/lottery.
 *
 * Copyright (c) Nodeloc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nodeloc\Lottery\Api\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Nodeloc\Lottery\Api\Serializers\LotterySerializer;
use Nodeloc\Lottery\Commands\DeleteLottery;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteLotteryController extends AbstractDeleteController
{
    /**
     * @var string
     */
    public $serializer = LotterySerializer::class;

    public $include = ['options'];

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Delete the resource.
     *
     * @param ServerRequestInterface $request
     */
    protected function delete(ServerRequestInterface $request)
    {
        return $this->bus->dispatch(
            new DeleteLottery(
                RequestUtil::getActor($request),
                Arr::get($request->getQueryParams(), 'id')
            )
        );
    }
}
