<?php

declare(strict_types=1);

namespace App\Messenger\Service;

class MessageSerializer
{
    public function serialize(?array $message): ?array
    {
        if (empty($message)) {
            return null;
        }
        return [
            'id'                => $message['id'],
            'conversationId'    => $message['conversation_id'],
            'userId'            => $message['user_id'],
            'time'              => $message['created_at'],
            'text'              => $message['text'],
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
