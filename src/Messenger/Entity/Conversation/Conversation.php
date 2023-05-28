<?php

declare(strict_types=1);

namespace App\Messenger\Entity\Conversation;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'conversation')]
final class Conversation
{
    private const TYPE_DIALOG       = 0;
    private const TYPE_CONVERSATION = 1;

    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $type;

    #[ORM\Column(type: 'integer')]
    private int $creatorId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lastMessageId;

    #[ORM\Column(type: 'integer')]
    private int $createdAt;

    private function __construct(
        int $type,
        int $creatorId,
    ) {
        $this->type = $type;
        $this->creatorId = $creatorId;
        $this->lastMessageId = null;
        $this->createdAt = time();
    }

    public static function createDialog(
        int $creatorId
    ): self {
        return new self(
            self::typeDialog(),
            $creatorId
        );
    }

    public static function createConversation(
        int $creatorId,
    ): self {
        return new self(
            self::typeConversation(),
            $creatorId
        );
    }

    public static function typeDialog(): int
    {
        return self::TYPE_DIALOG;
    }

    public static function typeConversation(): int
    {
        return self::TYPE_CONVERSATION;
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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function isDialog(): bool
    {
        return $this->type === self::TYPE_DIALOG;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function setCreatorId(int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function getLastMessageId(): ?int
    {
        return $this->lastMessageId;
    }

    public function setLastMessageId(?int $lastMessageId): void
    {
        $this->lastMessageId = $lastMessageId;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toArray(): array
    {
        return [
            'id'                => $this->getId(),
            'type'              => $this->getType(),
            'creator_id'        => $this->getCreatorId(),
            'last_message_id'   => $this->getLastMessageId(),
            'created_at'        => $this->getCreatedAt(),
        ];
    }
}
