<?php

declare(strict_types=1);

namespace App\Http\Action\Unifier\Messenger;

use App\Messenger\Entity\Conversation\Conversation;
use App\Messenger\Query\ConversationMember\GetInterlocutors\ConversationGetInterlocutorsFetcher;
use App\Messenger\Query\ConversationMember\GetInterlocutors\ConversationGetInterlocutorsQuery;
use App\Messenger\Query\Message\GetByIds\MessageGetByIdsFetcher;
use App\Messenger\Query\Message\GetByIds\MessageGetByIdsQuery;
use App\Messenger\Service\ConversationSerializer;
use App\Messenger\Service\MessageSerializer;
use ZayMedia\Shared\Http\Unifier\UnifierInterface;

final class ConversationUnifier implements UnifierInterface
{
    public function __construct(
        private readonly ConversationSerializer $conversationSerializer,
        private readonly MessageSerializer $messageSerializer,
        private readonly MessageGetByIdsFetcher $messageGetByIdsFetcher,
        private readonly ConversationGetInterlocutorsFetcher $conversationGetInterlocutorsFetcher,
    ) {
    }

    public function unifyOne(?int $userId, ?array $item): array
    {
        /** @var array{array} */
        $result = $this->unify($userId, (null !== $item) ? [$item] : []);
        return (isset($result[0])) ? $result[0] : [];
    }

    public function unify(?int $userId, array $items): array
    {
        $items = $this->conversationSerializer->serializeItems($items);

        $entityIds = $this->getEntityIds($items);

        if (null !== $userId) {
            $interlocutors = $this->getInterlocutors($userId, $entityIds['dialogIds']);
            $items = $this->mapInterlocutors($items, $interlocutors);
        }

        return $this->mapMessages($items, $this->getMessages($entityIds['messageIds']));
    }

    private function getInterlocutors(int $userId, array $ids): array
    {
        return $this->conversationGetInterlocutorsFetcher->fetch(
            new ConversationGetInterlocutorsQuery($userId, $ids)
        );
    }

    private function getMessages(array $ids): array
    {
        return $this->messageSerializer->serializeItems(
            $this->messageGetByIdsFetcher->fetch(
                new MessageGetByIdsQuery($ids)
            )
        );
    }

    private function mapInterlocutors(array $items, array $arrInterlocutors): array
    {
        /**
         * @var int $key
         * @var array{array{id:int|null, type:int}} $items
         */
        foreach ($items as $key => $item) {
            if ($item['type'] === Conversation::typeConversation()) {
                continue;
            }

            $items[$key]['userId'] = null;

            if (null !== $item['id']) {
                /** @var array{id:int, user_id:int, last_read_message_id:int} $interlocutor */
                foreach ($arrInterlocutors as $interlocutor) {
                    if ($item['id'] === $interlocutor['id']) {
                        $items[$key]['userId'] = $interlocutor['user_id'];
                        break;
                    }
                }
            }
        }

        return $items;
    }

    private function mapMessages(array $items, array $messages): array
    {
        /**
         * @var int $key
         * @var array{array{id: int, lastMessageId: int|null}} $items
         */
        foreach ($items as $key => $item) {
            if (!isset($item['lastMessageId'])) {
                continue;
            }

            $items[$key]['message'] = null;

            /** @var array{id:int} $message */
            foreach ($messages as $message) {
                if ($item['lastMessageId'] === $message['id']) {
                    $items[$key]['message'] = $message;

                    break;
                }
            }

            if (isset($items[$key]['lastMessageId'])) {
                unset($items[$key]['lastMessageId']);
            }
        }

        return $items;
    }

    /** @return array{conversationIds:array<int,int>, dialogIds:array<int,int>, messageIds:array<int,int>} */
    private function getEntityIds(array $items): array
    {
        $conversationIds    = [];
        $dialogIds          = [];
        $messageIds         = [];

        /** @var array{id: int, lastMessageId: int, type: int} $item */
        foreach ($items as $item) {
            if (isset($item['id']) && !empty($item['id'])) {
                $conversationIds[] = $item['id'];
            }

            if (isset($item['type']) && $item['type'] === Conversation::typeDialog()) {
                $dialogIds[] = $item['id'];
            }

            if (isset($item['lastMessageId']) && !empty($item['lastMessageId'])) {
                $messageIds[] = $item['lastMessageId'];
            }
        }

        /** @var array{conversationIds:array<int,int>, dialogIds:array<int,int>, messageIds:array<int,int>} */
        return [
            'conversationIds'   => array_unique($conversationIds),
            'dialogIds'         => array_unique($dialogIds),
            'messageIds'        => array_unique($messageIds),
        ];
    }
}
