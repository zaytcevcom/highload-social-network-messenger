<?php

declare(strict_types=1);

namespace App\Messenger\Entity\Message;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class MessageRepository
{
    /**
     * @var EntityRepository<Message>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Message::class);
        $this->em = $em;
    }

    public function getById(int $id): Message
    {
        if (!$message = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'messenger',
                message: 'error.messenger.message_not_found',
                code: 1
            );
        }

        return $message;
    }

    public function findById(int $id): ?Message
    {
        return $this->repo->findOneBy([
            'id'        => $id,
            'deletedAt' => null,
        ]);
    }

    public function findLastByConversationId(int $conversationId): ?Message
    {
        return $this->repo->findOneBy(
            [
                'conversationId' => $conversationId,
                'deletedAt' => null,
            ],
            [
                'createdAt' => 'DESC',
                'id' => 'DESC',
            ]
        );
    }

    public function add(Message $message): void
    {
        $this->em->persist($message);
    }

    public function remove(Message $message): void
    {
        $this->em->remove($message);
    }
}
