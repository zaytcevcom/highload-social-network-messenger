<?php

declare(strict_types=1);

namespace App\Messenger\Command\Conversation\Create;

use App\Messenger\Entity\Conversation\Conversation;
use App\Messenger\Entity\Conversation\ConversationRepository;
use App\Messenger\Entity\ConversationMember\ConversationMember;
use App\Messenger\Entity\ConversationMember\ConversationMemberRepository;
use App\Messenger\Query\Conversation\GetDialogIdByUserIds\ConversationGetDialogIdByUserIdsFetcher;
use App\Messenger\Query\Conversation\GetDialogIdByUserIds\ConversationGetDialogIdByUserIdsQuery;
use ZayMedia\Shared\Components\Flusher;

final class ConversationCreateHandler
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationMemberRepository $conversationMemberRepository,
        private readonly ConversationGetDialogIdByUserIdsFetcher $conversationGetDialogByUserIdsFetcher,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(ConversationCreateCommand $command): int
    {
        $conversationId = $this->conversationGetDialogByUserIdsFetcher->fetch(
            new ConversationGetDialogIdByUserIdsQuery(
                sourceId: $command->sourceId,
                targetId: $command->targetId
            )
        );

        if (null !== $conversationId) {
            return $conversationId;
        }

        $conversation = Conversation::createDialog($command->sourceId);

        $this->conversationRepository->add($conversation);
        $this->flusher->flush();

        $conversationMemberSource = ConversationMember::create($conversation->getId(), $command->sourceId);
        $conversationMemberTarget = ConversationMember::create($conversation->getId(), $command->targetId);

        $this->conversationMemberRepository->add($conversationMemberSource);
        $this->conversationMemberRepository->add($conversationMemberTarget);

        $this->flusher->flush();

        return $conversation->getId();
    }
}
