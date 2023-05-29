<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByConversationId;

use App\Messenger\Helper\MessageHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class MessageGetByConversationIdFetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly MessageHelper $messageHelper,
    ) {
    }

    /** @throws Exception */
    public function fetch(MessageGetByConversationIdQuery $query): array
    {
        $shardId = $this->messageHelper->getShardId($query->conversationId);

        $sql = 'SELECT m.shard_id, m.id, m.conversation_id, m.user_id, m.created_at, m.text FROM conversation_message m WHERE (m.shard_id = ' . $shardId . ') AND (m.conversation_id = ' . $query->conversationId . ') AND (m.deleted_at IS NULL) ORDER BY m.created_at DESC, m.id DESC LIMIT ' . $query->count . ' OFFSET ' . $query->offset;

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }
}
