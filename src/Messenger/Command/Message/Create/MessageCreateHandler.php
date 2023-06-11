<?php

declare(strict_types=1);

namespace App\Messenger\Command\Message\Create;

use App\Messenger\Command\Conversation\RefreshLastMessageId\ConversationRefreshLastMessageIdHandler;
use App\Messenger\Entity\Conversation\ConversationRepository;
use App\Messenger\Entity\ConversationMember\ConversationMemberRepository;
use App\Messenger\Entity\Message\Message;
use App\Messenger\Helper\MessageHelper;
use Doctrine\DBAL\Connection;
use Tarantool\Client\Client;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

use function App\Components\env;

final class MessageCreateHandler
{
    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationMemberRepository $conversationMemberRepository,
        private readonly ConversationRefreshLastMessageIdHandler $conversationRefreshLastMessageIdHandler,
        private readonly Client $tarantool,
        private readonly MessageHelper $messageHelper,
        private readonly Connection $connection,
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
            shardId: $this->messageHelper->getShardId($conversation->getId()),
            conversationId: $conversation->getId(),
            userId: $command->userId,
            text: $command->text,
        );

        if (env('TARANTOOL_ENABLE')) {
            $this->insertToTarantool($message);
        } else {
            $this->insertToMySql($message);
        }

        $this->conversationRefreshLastMessageIdHandler->handle($conversation->getId());
    }

    private function insertToTarantool(Message $message): void
    {
        $this->tarantool->call(
            'conversation_message_insert',
            $message->getConversationId(),
            $message->getUserId(),
            $message->getText(),
            $message->getCreatedAt()
        );
    }

    private function insertToMySql(Message $message): void
    {
        $sql = 'INSERT INTO conversation_message (shard_id, conversation_id, user_id, text, created_at, updated_at, deleted_at) VALUES (' . $message->getShardId() . ', ' . $message->getConversationId() . ', ' . $message->getUserId() . ', "' . $message->getText() . '", ' . $message->getCreatedAt() . ', NULL, NULL)';

        $this->connection->executeQuery($sql);
    }
}
