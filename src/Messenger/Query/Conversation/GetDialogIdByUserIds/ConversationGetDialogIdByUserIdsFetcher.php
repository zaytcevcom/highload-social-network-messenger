<?php

declare(strict_types=1);

namespace App\Messenger\Query\Conversation\GetDialogIdByUserIds;

use App\Messenger\Entity\Conversation\Conversation;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final class ConversationGetDialogIdByUserIdsFetcher
{
    public function __construct(
        private readonly Connection $connection
    ) {
    }

    /** @throws Exception */
    public function fetch(ConversationGetDialogIdByUserIdsQuery $query): int|null
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        /** @var array{id: int}|false $result */
        $result = $queryBuilder
            ->select('t.*')
            ->from('conversation', 't')
            ->innerJoin('t', 'conversation_member', 'tSource', 'tSource.conversation_id = t.id && tSource.user_id = :sourceId')
            ->innerJoin('t', 'conversation_member', 'tTarget', 'tTarget.conversation_id = t.id && tTarget.user_id = :targetId')
            ->where('t.type = :type')
            ->setParameter('type', Conversation::typeDialog())
            ->setParameter('sourceId', $query->sourceId)
            ->setParameter('targetId', $query->targetId)
            ->executeQuery()
            ->fetchAssociative();

        if ($result === false) {
            return null;
        }

        return $result['id'];
    }
}
