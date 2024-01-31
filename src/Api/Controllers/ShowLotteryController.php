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
use Nodeloc\Lottery\PollRepository;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowLotteryController extends AbstractShowController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = LotterySerializer::class;

    public $include = ['options', 'myVotes', 'myVotes.option'];

    public $optionalInclude = ['votes', 'votes.option', 'votes.user'];

    /**
     * @var PollRepository
     */
    protected $lottery;

    public function __construct(PollRepository $lottery)
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
