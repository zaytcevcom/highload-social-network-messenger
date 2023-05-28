<?php

declare(strict_types=1);

use App\Components\AccessTokenRepositoryStub;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Container\ContainerInterface;
use ZayMedia\Shared\Http\Middleware\Identity\BearerTokenValidator;

use function App\Components\env;

return [
    ResourceServer::class => static function (ContainerInterface $container): ResourceServer {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *    public_key_path:string
         * } $config
         */
        $config = $container->get('config')['oauth'];

        $repository = $container->get(AccessTokenRepositoryInterface::class);
        $publicKey = new CryptKey($config['public_key_path'], null, false);

        $validator = new BearerTokenValidator($repository);
        $validator->setPublicKey($publicKey);

        return new ResourceServer(
            $repository,
            $publicKey,
            $validator
        );
    },
    AccessTokenRepositoryInterface::class => DI\get(AccessTokenRepositoryStub::class),

    'config' => [
        'oauth' => [
            'scopes' => [
                'common',
            ],
            'clients' => [
                [
                    'name' => 'SERVER',
                    'client_id' => '1',
                    'redirect_uri' => 'default',
                ],
            ],
            'encryption_key' => env('JWT_ENCRYPTION_KEY', ''),
            'public_key_path' => env('JWT_PUBLIC_KEY_PATH', ''),
            'private_key_path' => env('JWT_PRIVATE_KEY_PATH', ''),
            'auth_code_interval' => 'PT1M',
            'access_token_interval' => 'PT230M',
            'refresh_token_interval' => 'P90D',
        ],
    ],
];
