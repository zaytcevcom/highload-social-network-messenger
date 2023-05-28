<?php

declare(strict_types=1);

namespace App\Http\Action\Unifier\Messenger;

use App\Messenger\Service\MessageSerializer;
use ZayMedia\Shared\Http\Unifier\UnifierInterface;

final class MessageUnifier implements UnifierInterface
{
    public function __construct(
        private readonly MessageSerializer $messageSerializer,
    ) {
    }

    public function unifyOne(?int $userId, ?array $item): array
    {
        /** @var array{array} */
        $result = $this->unify($userId, (null !== $item) ? [$item] : []);
        return (isset($result[0])) ? $result[0] : [];
    }

    public function unify(?int $userId, array $items): array
    {
        return $this->messageSerializer->serializeItems($items);
    }
}
