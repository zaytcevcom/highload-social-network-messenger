<?php

declare(strict_types=1);

namespace App\Messenger\Query\ConversationMember\GetOwnerIds;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ConversationMembersGetOwnerIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /**
     * @return int[]
     * @throws Exception
     */
    public function fetch(ConversationMembersGetOwnerIdsQuery $query): array
    {
        $sqlQuery = $this->connection->createQueryBuilder()
            ->select(['m.user_id'])
            ->from('conversation_member', 'm')
            ->andWhere('m.conversation_id = :conversationId')
            ->setParameter('conversationId', $query->conversationId);

        $result = $sqlQuery
            ->orderBy('m.id', 'DESC')
            ->executeQuery();

        $items = [];

        /** @var array{user_id: int} $row */
        foreach ($result->fetchAllAssociative() as $row) {
            $items[] = $row['user_id'];
        }

        return $items;
    }
}
