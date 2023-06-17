<?php

declare(strict_types=1);

namespace App\Messenger\Entity\ConversationMember;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ZayMedia\Shared\Http\Exception\DomainExceptionModule;

final class ConversationMemberRepository
{
    /** @var EntityRepository<ConversationMember> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(ConversationMember::class);
        $this->em = $em;
    }

    public function getById(int $id): ConversationMember
    {
        if (!$conversationMember = $this->findById($id)) {
            throw new DomainExceptionModule(
                module: 'messenger',
                message: 'error.messenger.conversation_member_not_found',
                code: 1
            );
        }

        return $conversationMember;
    }

    public function getByConversationAndUserIds(int $conversationId, int $userId): ConversationMember
    {
        if (!$conversationMember = $this->findByConversationAndUserIds($conversationId, $userId)) {
            throw new DomainExceptionModule(
                module: 'messenger',
                message: 'error.messenger.conversation_member_not_found',
                code: 1
            );
        }

        return $conversationMember;
    }

    public function findByConversationAndUserIds(int $conversationId, int $userId): ?ConversationMember
    {
        return $this->repo->findOneBy([
            'conversationId' => $conversationId,
            'userId' => $userId,
        ]);
    }

    public function isMember(int $conversationId, int $userId): bool
    {
        return $this->findByConversationAndUserIds($conversationId, $userId) !== null;
    }

    public function findById(int $id): ?ConversationMember
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function add(ConversationMember $conversationMember): void
    {
        $this->em->persist($conversationMember);
    }

    public function remove(ConversationMember $conversationMember): void
    {
        $this->em->remove($conversationMember);
    }
}
