<?php

declare(strict_types=1);

use Sentry\SentrySdk;
use ZayMedia\Shared\Components\Sentry;

return [
    Sentry::class => static fn (): Sentry => new Sentry(SentrySdk::getCurrentHub()),
];
