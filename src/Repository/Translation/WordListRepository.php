<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class WordListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordList::class);
    }

    public function findByChat(Chat $chat): ?WordList
    {
        /** @var WordList|null $wordList */
        $wordList = $this->findOneBy(['chat' => $chat]);

        return $wordList;
    }

    /**
     * @throws ORMException
     */
    public function addWordList(WordList $wordList):void
    {
        $this->getEntityManager()->persist($wordList);
    }
}
