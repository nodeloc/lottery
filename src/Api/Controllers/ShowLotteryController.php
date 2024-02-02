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
use Nodeloc\Lottery\LotteryRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowLotteryController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = LotterySerializer::class;

    public $include = [ 'lottery_participants'];

    public $optionalInclude = ['participants','participants.status', 'participants.user'];

    /**
     * @var LotteryRepository
     */
    protected $lottery;

    public function __construct(LotteryRepository $lottery)
    {
        $this->lottery = $lottery;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        return $this->lottery->findOrFail(
            Arr::get($request->getQueryParams(), 'id'),
            RequestUtil::getActor($request)
        );
    }
}
