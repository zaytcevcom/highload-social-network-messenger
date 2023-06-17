<?php

declare(strict_types=1);

namespace App\Messenger\Command\Message\Read;

use App\Messenger\Entity\Conversation\ConversationRepository;
use App\Messenger\Entity\ConversationMember\ConversationMemberRepository;
use ZayMedia\Shared\Components\Flusher;
use ZayMedia\Shared\Components\Queue\Queue;

final class MessageReadHandler
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationMemberRepository $conversationMemberRepository,
        private readonly Flusher $flusher,
        private readonly Queue $queue,
    ) {
    }

    public function handle(MessageReadCommand $command): void
    {
        $conversation = $this->conversationRepository->getById($command->conversationId);

        $conversationMember = $this->conversationMemberRepository->getByConversationAndUserIds(
            conversationId: $conversation->getId(),
            userId: $command->userId,
        );

        $conversationMember->setLastReadMessageId($command->messageId);

        $this->conversationMemberRepository->add($conversationMember);

        $this->flusher->flush();

        $value = $this->getCountUnreadMessages($conversation->getId(), $command->userId, $command->messageId);

        if ($value > 0) {
            $this->sendEventCounterDecrease($conversation->getId(), $command->userId, $value);
        }
    }

    /** @psalm-suppress UnusedParam */
    private function getCountUnreadMessages(int $conversationId, int $userId, int $messageId): int
    {
        // todo: Получить кол-во непрочитанных сообщений и передать это значение
        return 1;
    }

    private function sendEventCounterDecrease(int $conversationId, int $userId, int $value): void
    {
        $this->queue->publish(
            queue: 'conversation-counter-decrease',
            message: [
                'conversationId'    => $conversationId,
                'userId'            => $userId,
                'value'             => $value,
            ]
        );
    }
}
