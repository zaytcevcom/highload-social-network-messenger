<?php

declare(strict_types=1);

namespace App\Messenger\Query\ConversationMember\GetInterlocutors;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

use function ZayMedia\Shared\Components\Functions\toArrayString;

final class ConversationGetInterlocutorsFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /** @throws Exception */
    public function fetch(ConversationGetInterlocutorsQuery $query): array
    {
        $ids = toArrayString($query->ids);

        if (empty($ids)) {
            return [];
        }

        $result = $this->connection->createQueryBuilder()
            ->select(['conversation_id', 'user_id', 'last_read_message_id'])
            ->from('conversation_member')
            ->andWhere('conversation_id IN (:ids)')
            ->andWhere('user_id != :userId')
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER)
            ->setParameter('userId', $query->userId)
            ->executeQuery();

        $items = [];

        /** @var array{conversation_id: int, user_id: int, last_read_message_id: int} $row */
        foreach ($result->fetchAllAssociative() as $row) {
            $items[] = [
                'id'                    => $row['conversation_id'],
                'user_id'               => $row['user_id'],
                'last_read_message_id'  => $row['last_read_message_id'],
            ];
        }

        return $items;
    }
}
