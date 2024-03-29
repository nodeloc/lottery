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

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Nodeloc\Lottery\Api\Serializers\LotterySerializer;
use Nodeloc\Lottery\Commands\EnterLottery;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

/**
 * This class also works for single-enter lottery.
 * The existing API endpoint only allows for one enter per user, so we need to create a new one.
 */
class EnterLotteryController extends AbstractShowController
{
    /**
     * @var string
     */
    public $serializer = LotterySerializer::class;

    public $include = ['lottery_participants', 'lottery_participants.user'];

    public $optionalInclude = ['participants', 'participants.user'];

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
     * Get the data to be serialized and assigned to the response document.
     *
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        return $this->bus->dispatch(
            new EnterLottery(
                RequestUtil::getActor($request),
                Arr::get($request->getQueryParams(), 'id'),
            )
        );
    }
}
