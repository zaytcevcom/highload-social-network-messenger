<?php

declare(strict_types=1);

namespace App\Messenger\Query\ConversationMember\GetInterlocutors;

use Symfony\Component\Validator\Constraints as Assert;

final class ConversationGetInterlocutorsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly array $ids,
    ) {
    }
}
