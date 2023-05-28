<?php

declare(strict_types=1);

namespace App\Messenger\Service;

class ConversationSerializer
{
    public function serialize(?array $conversation): ?array
    {
        if (empty($conversation)) {
            return null;
        }

        return [
            'id'            => $conversation['id'],
            'type'          => $conversation['type'],
            'lastMessageId' => $conversation['last_message_id'],
        ];
    }

    public function serializeItems(array $items): array
    {
        $result = [];

        /** @var array $item */
        foreach ($items as $item) {
            $result[] = $this->serialize($item);
        }

        return $result;
    }
}
