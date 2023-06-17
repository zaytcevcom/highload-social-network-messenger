<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use ZayMedia\Shared\Components\Queue\Queue;
use ZayMedia\Shared\Components\Queue\RabbitMQ;

use function App\Components\env;

return [
    Queue::class => static function (ContainerInterface $container): Queue {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     host:string,
         *     port:integer,
         *     user:string,
         *     password:string
         * } $config
         */
        $config = $container->get('config')['queue-rabbit'];

        return new RabbitMQ(
            host: $config['host'],
            port: $config['port'],
            user: $config['user'],
            password: $config['password']
        );
    },

    'config' => [
        'queue-rabbit' => [
            'host' => env('RABBITMQ_HOST'),
            'port' => (int)env('RABBITMQ_PORT'),
            'user' => env('RABBITMQ_USER'),
            'password' => env('RABBITMQ_PASSWORD'),
        ],
    ],
];
