<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Messenger\Command\Message\Read\MessageReadCommand;
use App\Messenger\Command\Message\Read\MessageReadHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\Security;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

#[OA\Post(
    path: '/conversations/{id}/messages/{messageId}/read',
    description: 'Пометить сообщение как прочитанное',
    summary: 'Пометить сообщение как прочитанное',
    security: [Security::BEARER_AUTH],
    tags: ['Messenger']
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
    name: 'messageId',
    description: 'Идентификатор сообщения',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class ReadAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly MessageReadHandler $handler,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = new MessageReadCommand(
            userId: $identity->id,
            conversationId: Route::getArgumentToInt($request, 'id'),
            messageId: Route::getArgumentToInt($request, 'messageId'),
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
