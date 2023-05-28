<?php

declare(strict_types=1);

use App\Http\Action;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use ZayMedia\Shared\Components\Router\StaticRouteGroup as Group;

return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', Action\V1\OpenApiAction::class);

        $group->group('/conversations', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', Action\V1\Messenger\Conversations\GetByUserIdAction::class);
            $group->post('/dialog/{userId}', Action\V1\Messenger\Conversations\CreateAction::class);
            $group->get('/{id}/messages', Action\V1\Messenger\Messages\GetByConversationIdAction::class);
            $group->post('/{id}/messages', Action\V1\Messenger\Messages\CreateAction::class);
        }));
    }));
};
