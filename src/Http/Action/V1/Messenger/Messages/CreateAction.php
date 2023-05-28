<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Messenger\Messages;

use App\Messenger\Command\Message\Create\MessageCreateCommand;
use App\Messenger\Command\Message\Create\MessageCreateHandler;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Components\Router\Route;
use ZayMedia\Shared\Components\Serializer\Denormalizer;
use ZayMedia\Shared\Components\Validator\Validator;
use ZayMedia\Shared\Http\Middleware\Identity\Authenticate;
use ZayMedia\Shared\Http\Response\JsonDataSuccessResponse;

#[OA\Post(
    path: '/conversations/{id}/messages',
    description: 'Отправка сообщения<br><br>
    **Коды ошибок**:<br>
    **1** - Доступ запрещен<br>',
    summary: 'Отправка сообщения',
    security: [['bearerAuth' => '{}']],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'text',
                    type: 'string',
                    example: 'Новое сообщение!'
                ),
            ]
        )
    ),
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
#[OA\Response(
    response: '200',
    description: 'Successful operation'
)]
final class CreateAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly Denormalizer $denormalizer,
        private readonly MessageCreateHandler $handler,
        private readonly Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = $this->denormalizer->denormalize(
            array_merge((array)$request->getParsedBody(), [
                'userId'            => $identity->id,
                'conversationId'    => Route::getArgumentToInt($request, 'id'),
            ]),
            MessageCreateCommand::class
        );

        $this->validator->validate($command);

        $this->handler->handle($command);

        return new JsonDataSuccessResponse();
    }
}
