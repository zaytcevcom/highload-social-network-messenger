<?php

declare(strict_types=1);

namespace App\Messenger\Entity\Message;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'conversation_message')]
#[ORM\Index(fields: ['conversationId'], name: 'IDX_CONVERSATION')]
#[ORM\Index(fields: ['userId'], name: 'IDX_USER')]
#[ORM\Index(fields: ['createdAt'], name: 'IDX_CREATED_AT')]
final class Message
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $shardId;

    #[ORM\Column(type: 'integer')]
    private int $conversationId;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(type: 'integer')]
    private int $createdAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $updatedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $deletedAt = null;

    private function __construct(
        int $shardId,
        int $conversationId,
        int $userId,
        string $text,
    ) {
        $this->shardId        = $shardId;
        $this->conversationId = $conversationId;
        $this->userId         = $userId;
        $this->text           = $text;
        $this->createdAt      = time();
    }

    public static function create(
        int $shardId,
        int $conversationId,
        int $userId,
        string $text,
    ): self {
        return new self(
            shardId: $shardId,
            conversationId: $conversationId,
            userId: $userId,
            text: $text
        );
    }

    public function edit(
        string $text,
    ): void {
        $this->setText($text);

        $this->setUpdatedAt(time());
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

    public function getShardId(): int
    {
        return $this->shardId;
    }

    public function setShardId(int $shardId): void
    {
        $this->shardId = $shardId;
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?int $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function toArray(): array
    {
        return [
            'id'              => $this->getId(),
            'conversation_id' => $this->getConversationId(),
            'user_id'         => $this->getUserId(),
            'text'            => $this->getText(),
            'created_at'      => $this->getCreatedAt(),
            'updated_at'      => $this->getUpdatedAt(),
            'deleted_at'      => $this->getDeletedAt(),
        ];
    }
}
