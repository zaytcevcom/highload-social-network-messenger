<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use function App\Components\env;

date_default_timezone_set('UTC');
http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

try {
    Sentry\init(['dsn' => env('SENTRY_DSN'), 'environment' => env('APP_ENV')]);
} catch (RuntimeException $e) {
}

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$app = (require __DIR__ . '/../config/app.php')($container);
$app->run();
