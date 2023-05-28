<?php

declare(strict_types=1);

namespace App\Messenger\Entity\ConversationMember;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'conversation_member')]
#[ORM\Index(fields: ['conversationId'], name: 'IDX_CONVERSATION')]
#[ORM\Index(fields: ['userId'], name: 'IDX_USER')]
final class ConversationMember
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $conversationId;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lastReadMessageId;

    private function __construct(
        int $conversationId,
        int $userId,
    ) {
        $this->conversationId       = $conversationId;
        $this->userId               = $userId;
        $this->lastReadMessageId    = null;
    }

    public static function create(
        int $conversationId,
        int $userId,
    ): self {
        return new self(
            conversationId: $conversationId,
            userId: $userId,
        );
    }

    public function getId(): int
    {
        if (null === $this->id) {
            throw new DomainException('Id not set');
        }
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function setConversationId(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getLastReadMessageId(): ?int
    {
        return $this->lastReadMessageId;
    }

    public function setLastReadMessageId(?int $lastReadMessageId): void
    {
        $this->lastReadMessageId = $lastReadMessageId;
    }
}
