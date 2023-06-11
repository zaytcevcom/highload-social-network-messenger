<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByConversationId;

use App\Messenger\Helper\MessageHelper;
use Doctrine\DBAL\Connection;
use Tarantool\Client\Client;

use function App\Components\env;

final class MessageGetByConversationIdFetcher
{
    public function __construct(
        private readonly Connection $connection,
        private readonly MessageHelper $messageHelper,
        private readonly Client $tarantool,
    ) {
    }

    public function fetch(MessageGetByConversationIdQuery $query): array
    {
        if (env('TARANTOOL_ENABLE')) {
            return $this->selectFromTarantool($query);
        }

        return $this->selectFromMySql($query);
    }

    private function selectFromTarantool(MessageGetByConversationIdQuery $query): array
    {
        $rows = $this->tarantool->call('conversation_messages_select', $query->conversationId, $query->count, $query->offset);

        $result = [];

        /** @var string[] $row */
        foreach ($rows[0] as $row) {
            $result[] = [
                'id'                => $row[0] ?? null,
                'conversation_id'   => $row[1] ?? null,
                'user_id'           => $row[2] ?? null,
                'text'              => $row[3] ?? null,
                'created_at'        => $row[4] ?? null,
            ];
        }

        return $result;
    }

    private function selectFromMySql(MessageGetByConversationIdQuery $query): array
    {
        $shardId = $this->messageHelper->getShardId($query->conversationId);

        $sql = 'SELECT m.shard_id, m.id, m.conversation_id, m.user_id, m.created_at, m.text FROM conversation_message m WHERE (m.shard_id = ' . $shardId . ') AND (m.conversation_id = ' . $query->conversationId . ') AND (m.deleted_at IS NULL) ORDER BY m.created_at DESC, m.id DESC LIMIT ' . $query->count . ' OFFSET ' . $query->offset;

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }
}
