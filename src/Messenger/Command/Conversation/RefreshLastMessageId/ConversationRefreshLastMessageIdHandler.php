<?php

declare(strict_types=1);

namespace App\Messenger\Command\Conversation\RefreshLastMessageId;

use App\Messenger\Entity\Conversation\ConversationRepository;
use App\Messenger\Entity\Message\MessageRepository;
use ZayMedia\Shared\Components\Flusher;

final class ConversationRefreshLastMessageIdHandler
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(int $conversationId): void
    {
        $conversation = $this->conversationRepository->getById($conversationId);

        $lastMessage = $this->messageRepository->findLastByConversationId($conversation->getId());

        $conversation->setLastMessageId($lastMessage?->getId());

        $this->conversationRepository->add($conversation);

        $this->flusher->flush();
    }
}
