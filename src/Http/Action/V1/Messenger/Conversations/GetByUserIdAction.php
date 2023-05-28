<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Conversations;

use App\Http\Action\Unifier\Messenger\ConversationUnifier;
use App\Messenger\Query\Conversation\GetByUserId\ConversationGetByUserIdFetcher;
use App\Messenger\Query\Conversation\GetByUserId\ConversationGetByUserIdQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataItemsResponse;

#[OA\Get(
    path: '/conversations',
    description: 'Получение списка бесед пользователя',
    summary: 'Получение списка бесед пользователя',
    security: [['bearerAuth' => '{}']],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'search',
    description: 'Поисковый запрос',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'string'
    ),
)]
#[OA\Parameter(
    name: 'sort',
    description: 'Сортировка (0 - по убыванию, 1 - по возрастания)',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 0
)]
#[OA\Parameter(
    name: 'count',
    description: 'Кол-во которое необходимо получить',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 100
)]
#[OA\Parameter(
    name: 'offset',
    description: 'Смещение',
    in: 'query',
    required: false,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 0
)]
final class GetByUserIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly ConversationGetByUserIdFetcher $fetcher,
        private readonly Validator $validator,
        private readonly ConversationUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $query = $this->denormalizer->denormalizeQuery(
            array_merge(
                $request->getQueryParams(),
                ['userId' => $identity->id]
            ),
            ConversationGetByUserIdQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        $items = $this->unifier->unify($identity->id, $result->items);

        return new JsonDataItemsResponse(
            count: $result->count,
            items: $items
        );
    }
}
