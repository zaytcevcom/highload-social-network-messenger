<?php

declare(strict_types=1);

namespace App\Messenger\Command\Message\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class MessageCreateCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        #[Assert\NotBlank]
        public readonly int $conversationId,
        public readonly string $text,
    ) {
    }
}
