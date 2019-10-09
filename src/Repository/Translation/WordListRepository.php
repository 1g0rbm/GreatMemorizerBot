<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\DBALException;
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
     * @throws DBALException
     */
    public function findDistinctByChat(Chat $chat): array
    {
        $query = <<<SQL
SELECT w.text, wl.id as word_list_id
FROM word_lists wl
JOIN lists2words l2w ON wl.id = l2w.word_list_id
JOIN words w on l2w.word_id = w.id
WHERE wl.chat_id = :chat_id
GROUP BY w.text, wl.id
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute(['chat_id' => $chat->getId()]);

        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * @throws ORMException
     */
    public function addWordList(WordList $wordList):void
    {
        $this->getEntityManager()->persist($wordList);
    }
}