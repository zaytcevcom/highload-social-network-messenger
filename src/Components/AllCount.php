<?php

declare(strict_types=1);

namespace App\Components;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final class AllCount
{
    /** @throws Exception */
    public static function get(QueryBuilder $query, string $field = 'id'): int
    {
        $result = $query
            ->select('COUNT(' . $field . ') AS count')
            ->setFirstResult(0)
            ->fetchAssociative();

        return (int)($result['count'] ?? 0);
    }
}
