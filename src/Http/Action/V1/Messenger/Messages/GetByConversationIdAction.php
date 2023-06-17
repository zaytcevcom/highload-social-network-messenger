<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Http\Action\Unifier\Messenger\MessageUnifier;
use App\Messenger\Query\Message\GetByConversationId\MessageGetByConversationIdFetcher;
use App\Messenger\Query\Message\GetByConversationId\MessageGetByConversationIdQuery;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataResponse;

#[OA\Get(
    path: '/conversations/{id}/messages',
    description: 'Получение списка сообщений беседы',
    summary: 'Получение списка сообщений беседы',
    security: [Security::BEARER_AUTH],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор беседы',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
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
final class GetByConversationIdAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly MessageGetByConversationIdFetcher $fetcher,
        private readonly Validator $validator,
        private readonly MessageUnifier $unifier
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);
        $conversationId = Route::getArgumentToInt($request, 'id');

        /** @var string[] $data */
        $data = $request->getQueryParams();

        $query = $this->denormalizer->denormalizeQuery(
            array_merge(
                $data,
                [
                    'userId'            => $identity->id,
                    'conversationId'    => $conversationId,
                ]
            ),
            MessageGetByConversationIdQuery::class
        );

        $this->validator->validate($query);

        $result = $this->fetcher->fetch($query);

        return new JsonDataResponse(
            data: $this->unifier->unify($identity->id, $result)
        );
    }
}
