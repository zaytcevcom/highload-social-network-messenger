<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Tarantool\Client\Client;

use function App\Components\env;

return [
    Client::class => static function (ContainerInterface $container): Client {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *    host:string,
         *    port:string,
         *    username:string,
         *    password:string,
         * } $config
         */
        $config = $container->get('config')['tarantool'];

        return Client::fromOptions([
            'uri' => 'tcp://' . $config['host'] . ':' . $config['port'],
            // 'username' => $config['username'],
            // 'password' => $config['password']
        ]);
    },

    'config' => [
        'tarantool' => [
            'host' => env('TARANTOOL_HOST'),
            'port' => env('TARANTOOL_PORT'),
            'username' => env('TARANTOOL_USER'),
            'password' => env('TARANTOOL_PASSWORD'),
        ],
    ],
];
