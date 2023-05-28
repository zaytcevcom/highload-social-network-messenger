<?php

declare(strict_types=1);

namespace App\Messenger\Command\Message\Create;

use App\Messenger\Command\Conversation\RefreshLastMessageId\ConversationRefreshLastMessageIdHandler;
use App\Messenger\Entity\Conversation\ConversationRepository;
use App\Messenger\Entity\ConversationMember\ConversationMemberRepository;
use App\Messenger\Entity\Message\Message;
use App\Messenger\Entity\Message\MessageRepository;
use ZayMedia\Shared\Components\Flusher;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class MessageCreateHandler
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationMemberRepository $conversationMemberRepository,
        private readonly ConversationRefreshLastMessageIdHandler $conversationRefreshLastMessageIdHandler,
        private readonly MessageRepository $messageRepository,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(MessageCreateCommand $command): void
    {
        $conversation = $this->conversationRepository->getById($command->conversationId);

        if (!$this->conversationMemberRepository->isMember($conversation->getId(), $command->userId)) {
            throw new DomainExceptionModule(
                module: 'messenger',
                message: 'error.messenger.permission_denied',
                code: 1
            );
        }

        $message = Message::create(
            conversationId: $conversation->getId(),
            userId: $command->userId,
            text: $command->text,
        );

        $this->messageRepository->add($message);

        $this->flusher->flush();

        $this->conversationRefreshLastMessageIdHandler->handle($conversation->getId());
    }
}
