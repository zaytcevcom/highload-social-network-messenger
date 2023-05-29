<?php

declare(strict_types=1);

namespace App\Messenger\Helper;

class MessageHelper
{
    public function getShardId(int $conversationId): int
    {
        $shardCount = 2;
        $hash = crc32((string)$conversationId);
        return abs($hash % $shardCount);
    }
}
