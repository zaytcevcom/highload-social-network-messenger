<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByIds;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use ZayMedia\Shared\Helpers\Helper;

use function ZayMedia\Shared\Components\Functions\toArrayString;

final class MessageGetByIdsFetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function fetch(MessageGetByIdsQuery $query): array
    {
        $ids = toArrayString($query->ids);

        if (empty($ids)) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(['*'])
            ->from('conversation_message')
            ->andWhere('id IN (:ids)')
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER);

        /** @var array{array} $rows */
        $rows = $queryBuilder
            ->setMaxResults(1000)
            ->executeQuery()
            ->fetchAllAssociative();

        return Helper::sortItemsByIds($rows, $ids);
    }
}
