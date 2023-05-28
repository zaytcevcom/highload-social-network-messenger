<?php

declare(strict_types=1);

namespace App\Messenger\Query\Conversation\GetByUserId;

use Symfony\Component\Validator\Constraints as Assert;

final class ConversationGetByUserIdQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        public readonly string $search = '',
        public readonly int $sort = 0,
        public readonly int $count = 100,
        public readonly int $offset = 0,
    ) {
    }
}
