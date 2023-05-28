<?php

declare(strict_types=1);

namespace App\Messenger\Entity\Conversation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class ConversationRepository
{
    /** @var EntityRepository<Conversation> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Conversation::class);
        $this->em = $em;
    }

    public function getById(int $id): Conversation
    {
        if (!$conversation = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'messenger',
                message: 'error.messenger.conversation_not_found',
                code: 1
            );
        }

        return $conversation;
    }

    public function findById(int $id): ?Conversation
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function add(Conversation $conversation): void
    {
        $this->em->persist($conversation);
    }

    public function remove(Conversation $conversation): void
    {
        $this->em->remove($conversation);
    }
}
