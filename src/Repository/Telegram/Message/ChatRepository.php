<?php

namespace Ig0rbm\Memo\Repository\Telegram\Message;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function findChatById(int $id): ?Chat
    {
        /** @var null|Chat $chat */
        $chat = $this->find($id);

        return $chat;
    }

    /**
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function addChat(Chat $chat): void
    {
        $this->getEntityManager()->persist($chat);
    }
}