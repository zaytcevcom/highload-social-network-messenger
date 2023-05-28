<?php

declare(strict_types=1);

use Middlewares\ContentLanguage;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use ZayMedia\Shared\Http\Middleware;

return static function (App $app): void {
    $app->add(Middleware\Identity\Authenticate::class);
    // $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(Middleware\AccessDeniedExceptionHandler::class);
    $app->add(Middleware\MethodNotAllowedExceptionHandler::class);
    $app->add(Middleware\HttpNotFoundExceptionHandler::class);
    $app->add(Middleware\DomainExceptionModuleHandler::class);
    $app->add(Middleware\NotFoundExceptionModuleHandler::class);
    $app->add(Middleware\DenormalizationExceptionHandler::class);
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(Middleware\InvalidArgumentExceptionHandler::class);
    $app->add(Middleware\UnauthorizedHttpExceptionHandler::class);
    $app->add(Middleware\ClearEmptyInput::class);
    $app->add(Middleware\TranslatorLocale::class);
    $app->add(ContentLanguage::class);
    $app->addBodyParsingMiddleware();
    $app->add(ErrorMiddleware::class);
};
