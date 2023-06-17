<?php

declare(strict_types=1);

namespace App\Messenger\Command\Message\Read;

use Symfony\Component\Validator\Constraints as Assert;

final class MessageReadCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $conversationId,
        #[Assert\NotBlank]
        public readonly int $messageId,
    ) {
    }
}
