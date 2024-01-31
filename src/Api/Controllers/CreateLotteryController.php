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

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Bus\Dispatcher;
use Flarum\Http\RequestUtil;
use Flarum\Post\PostRepository;
use Nodeloc\Lottery\Api\Serializers\LotterySerializer;
use Nodeloc\Lottery\Commands\CreateLottery;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateLotteryController extends AbstractCreateController
{
    public $serializer = LotterySerializer::class;

    public $include = ['options'];

    /**
     * @var PostRepository
     */
    protected $posts;

    /**
     * @var Dispatcher
     */
    protected $bus;

    public function __construct(PostRepository $posts, Dispatcher $bus)
    {
        $this->posts = $posts;
        $this->bus = $bus;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $postId = Arr::get($request->getParsedBody(), 'data.relationships.post.data.id');

        return $this->bus->dispatch(
            new CreateLottery(
                RequestUtil::getActor($request),
                $this->posts->findOrFail($postId),
                Arr::get($request->getParsedBody(), 'data.attributes')
            )
        );
    }
}
