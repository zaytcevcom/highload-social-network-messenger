<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByConversationId;

use App\Components\AllCount;
use App\Components\ResultCountItems;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class MessageGetByConversationIdFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /** @throws Exception */
    public function fetch(MessageGetByConversationIdQuery $query): ResultCountItems
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $sqlQuery = $queryBuilder
            ->select('m.*')
            ->from('conversation_message', 'm')
            ->andWhere('m.conversation_id = :conversationId')
            ->andWhere('m.deleted_at IS NULL')
            ->setParameter('conversationId', $query->conversationId);

        $result = $sqlQuery
            ->orderBy('m.created_at', 'DESC')
            ->addOrderBy('m.id', 'DESC')
            ->setMaxResults($query->count)
            ->setFirstResult($query->offset)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        return new ResultCountItems(
            AllCount::get($sqlQuery),
            $rows
        );
    }
}
