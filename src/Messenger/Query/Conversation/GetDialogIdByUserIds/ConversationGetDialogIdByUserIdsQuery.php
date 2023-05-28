<?php

declare(strict_types=1);

namespace App\Messenger\Query\Conversation\GetDialogIdByUserIds;

use Symfony\Component\Validator\Constraints as Assert;

final class ConversationGetDialogIdByUserIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $sourceId,
        #[Assert\NotBlank]
        public readonly int $targetId,
    ) {
    }
}
