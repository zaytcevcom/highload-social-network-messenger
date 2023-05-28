<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByConversationId;

use Symfony\Component\Validator\Constraints as Assert;

final class MessageGetByConversationIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $conversationId,
        public readonly int $count = 100,
        public readonly int $offset = 0,
    ) {
    }
}
