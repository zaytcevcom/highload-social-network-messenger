<?php

declare(strict_types=1);

namespace App\Components;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

final class AccessTokenRepositoryStub implements AccessTokenRepositoryInterface
{
    /**
     * @psalm-suppress InvalidResponseType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @param mixed|null $userIdentifier
     */
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ) {
        return '';
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        // do nothing
    }

    public function revokeAccessToken($tokenId): void
    {
        // do nothing
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return false;
    }
}
