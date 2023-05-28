<?php

declare(strict_types=1);

namespace App\Messenger\Command\Conversation\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class ConversationCreateCommand
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $sourceId,
        #[Assert\NotBlank]
        public readonly int $targetId,
    ) {
    }
}
