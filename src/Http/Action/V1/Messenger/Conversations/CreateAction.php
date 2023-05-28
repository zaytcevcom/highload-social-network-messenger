<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Conversations;

use App\Messenger\Command\Conversation\Create\ConversationCreateCommand;
use App\Messenger\Command\Conversation\Create\ConversationCreateHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Helpers\OpenApi\ResponseSuccessful;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataResponse;

#[OA\Post(
    path: '/conversations/dialog/{userId}',
    description: 'Создание диалога с пользователем',
    summary: 'Создание диалога с пользователем',
    security: [['bearerAuth' => '{}']],
    tags: ['Messenger'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'userId',
    description: 'Идентификатор пользователя',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
final class CreateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly ConversationCreateHandler $handler,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = new ConversationCreateCommand(
            sourceId: $identity->id,
            targetId: Route::getArgumentToInt($request, 'userId')
        );

        $this->validator->validate($command);

        $conversationId = $this->handler->handle($command);

        return new JsonDataResponse([
            'id' => $conversationId,
        ]);
    }
}
