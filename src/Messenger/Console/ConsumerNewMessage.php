<?php

declare(strict_types=1);

namespace App\Messenger\Console;

use App\Messenger\Query\ConversationMember\GetOwnerIds\ConversationMembersGetOwnerIdsFetcher;
use App\Messenger\Query\ConversationMember\GetOwnerIds\ConversationMembersGetOwnerIdsQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZayMedia\Shared\Components\Queue\Queue;

final class ConsumerNewMessage extends Command
{
    public function __construct(
        private readonly Queue $queue,
        private readonly ConversationMembersGetOwnerIdsFetcher $conversationMembersGetOwnerIdsFetcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('messenger:consumer-new-message')
            ->setDescription('Messenger consumer new message command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callback = function (object $msg) use ($output): void {
            /**
             * @var array{
             *     conversationId:int,
             *     messageId:int,
             *     userId:int,
             * } $info
             */
            $info = json_decode((string)$msg->body, true);

            $output->writeln('<info>[MessageId]</info> - ' . $info['messageId']);

            $userIds = $this->conversationMembersGetOwnerIdsFetcher->fetch(
                new ConversationMembersGetOwnerIdsQuery($info['conversationId'])
            );

            foreach ($userIds as $userId) {
                if ($userId === $info['userId']) {
                    continue;
                }

                $this->sendEventCounterIncrease(
                    conversationId: $info['conversationId'],
                    userId: $userId
                );

                $output->writeln('<info>UserId - ' . $userId);
            }
        };

        $this->queue->consume(
            queue: 'new-message',
            callback: $callback
        );

        return 0;
    }

    private function sendEventCounterIncrease(int $conversationId, int $userId): void
    {
        $this->queue->publish(
            queue: 'conversation-counter-increase',
            message: [
                'conversationId'    => $conversationId,
                'userId'            => $userId,
                'value'             => 1,
            ]
        );
    }
}
