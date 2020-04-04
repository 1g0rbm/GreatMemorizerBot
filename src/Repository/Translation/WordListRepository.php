<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Doctrine\ORM\NonUniqueResultException;

class WordListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordList::class);
    }

    /**
     * @param Chat $chat
     * @param int[] $pos
     * @return int
     * @throws DBALException
     */
    public function countAllWordsForChatAndPos(Chat $chat, array $pos): int
    {
        $posPlaceHolder = array_map(fn(string $item) => ':' . $item, $pos);
        $pos            = array_combine($posPlaceHolder, $pos);
        $posPlaceHolder = implode(', ', $posPlaceHolder);

        $query = <<<SQL
SELECT COUNT(w.text) AS cnt
FROM word_lists wl
JOIN lists2words l2w ON wl.id = l2w.word_list_id
JOIN words w on l2w.word_id = w.id AND w.lang_code = 'en'
WHERE wl.chat_id = :chat_id AND w.pos IN({$posPlaceHolder})
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute(array_merge(['chat_id' => $chat->getId()], $pos));

        return $stmt->fetch()['cnt'];
    }

    /**
     * @throws DBALException
     */
    public function countUniqueWords(Chat $chat): int
    {
        $query = <<<SQL
SELECT COUNT(DISTINCT(w.text)) AS cnt
FROM word_lists wl
JOIN lists2words l2w ON wl.id = l2w.word_list_id
JOIN words w on l2w.word_id = w.id
WHERE wl.chat_id = :chat_id
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute(['chat_id' => $chat->getId()]);

        return $stmt->fetch()['cnt'];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getOneByChat(Chat $chat): WordList
    {
        $wordList = $this->findOneByChat($chat);
        if ($wordList === null) {
            throw WordListException::becauseThereIsNotListForChat($chat->getId());
        }

        return $wordList;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByChat(Chat $chat): ?WordList
    {
        $qb = $this->createQueryBuilder('wl')
            ->where('wl.chat = :chat')
            ->setParameter('chat', $chat);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getOneById(int $wordListId): WordList
    {
        /** @var WordList|null $wordList */
        $wordList = $this->findOneBy(['id' => $wordListId]);
        if ($wordList === null) {
            throw WordListException::becauseThereIsNotListForId($wordListId);
        }

        return $wordList;
    }

    public function findByChat(Chat $chat): ?WordList
    {
        /** @var WordList|null $wordList */
        $wordList = $this->findOneBy(['chat' => $chat]);

        return $wordList;
    }

    /**
     * @return mixed[]
     * @throws DBALException
     */
    public function findDistinctByChatAndLimit(Chat $chat, int $limit, int $offset)
    {
        $query = <<<SQL
SELECT w.text, wl.id as word_list_id
FROM word_lists wl
JOIN lists2words l2w ON wl.id = l2w.word_list_id
JOIN words w on l2w.word_id = w.id
WHERE wl.chat_id = :chat_id
GROUP BY w.text, wl.id
LIMIT :limit OFFSET :offset
SQL;

        $stmt = $this->getEntityManager()->getConnection()->prepare($query);
        $stmt->execute([
            'chat_id' => $chat->getId(),
            'limit'   => $limit,
            'offset'  => $offset
        ]);

        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);
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
