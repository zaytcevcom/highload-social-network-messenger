<?php

declare(strict_types=1);

namespace App\Messenger\Query\Message\GetByIds;

use Symfony\Component\Validator\Constraints as Assert;

final class MessageGetByIdsQuery
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly array $ids,
    ) {
    }
}
