<?php

declare(strict_types=1);

namespace App\Messenger\Query\Conversation\GetByUserId;

use App\Components\AllCount;
use App\Components\ResultCountItems;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ConversationGetByUserIdFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /** @throws Exception */
    public function fetch(ConversationGetByUserIdQuery $query): ResultCountItems
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $sqlQuery = $queryBuilder
            ->select([
                't.*',
                'o.last_read_message_id',
            ])
            ->from('conversation', 't')
            ->innerJoin('t', 'conversation_member', 'o', 't.id = o.conversation_id')
            ->andWhere('o.user_id = :userId')
            ->setParameter('userId', $query->userId);

        $result = $sqlQuery
            ->orderBy('t.last_message_id', 'DESC')
            ->addOrderBy('t.id', 'DESC')
            ->setMaxResults($query->count)
            ->setFirstResult($query->offset)
            ->executeQuery();

        $rows = $result->fetchAllAssociative();

        return new ResultCountItems(
            AllCount::get($sqlQuery, 't.id'),
            $rows
        );
    }
}
