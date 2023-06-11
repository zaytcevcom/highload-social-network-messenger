<?php

declare(strict_types=1);

namespace App\Messenger\Console;

use App\Messenger\Command\Message\Create\MessageCreateCommand;
use App\Messenger\Command\Message\Create\MessageCreateHandler;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateMessagesCommand extends Command
{
    public function __construct(
        private readonly MessageCreateHandler $messageCreateHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('generate:messages')
            ->setDescription('Generate 1 000 000 messages command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Start generate messages!</info>');

        $batch = 50000;

        for ($i = 0; $i <= 1_000_000; ++$i) {
            $this->messageCreateHandler->handle(
                new MessageCreateCommand(
                    userId: 1,
                    conversationId: 1,
                    text: Factory::create()->text()
                )
            );

            if ($i > 0 && $i % $batch === 0) {
                $output->writeln('<info>Generated: ' . $i . '</info>');
            }
        }

        $output->writeln('<info>Done!</info>');

        return 0;
    }
}
