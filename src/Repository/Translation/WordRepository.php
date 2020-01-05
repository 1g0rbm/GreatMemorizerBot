<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\Memo\Entity\Translation\Word;

use function array_map;
use function shuffle;

class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    /**
     * @return Collection|Word[]
     * @throws DBALException
     */
    public function getRandomWordsByWordListId(string $langCode, string $pos, int $wordListId, int $limit): Collection
    {
        $conn     = $this->getConnection();
        $idsQuery = 'SELECT w.id, w.text 
                     FROM memo.public.words w
                     JOIN lists2words l2w on w.id = l2w.word_id and l2w.word_list_id = :wordListId
                     WHERE w.lang_code = :langCode
                         AND w.pos = :pos
                     ORDER BY random()
                     LIMIT :limit';

        $stmt = $conn->prepare($idsQuery);
        $stmt->execute([
            'langCode'   => $langCode,
            'pos'        => $pos,
            'wordListId' => $wordListId,
            'limit'      => $limit
        ]);

        $words = $this->findWordsByIds(array_map(static fn(array $item) => $item['id'], $stmt->fetchAll()));

        shuffle($words);
        return new ArrayCollection($words);
    }

    /**
     * @return ArrayCollection|Word[]
     * @throws DBALException
     */
    public function getRandomWords(string $langCode, string $pos, int $limit): ArrayCollection
    {
        $conn     = $this->getConnection();
        $idsQuery = 'SELECT w.id 
                     FROM memo.public.words w
                     WHERE w.lang_code = :langCode
                     AND w.pos = :pos
                     ORDER BY random()
                     LIMIT :limit';

        $stmt = $conn->prepare($idsQuery);
        $stmt->execute([
            'langCode' => $langCode,
            'pos' => $pos,
            'limit' => $limit
        ]);

        $words = $this->findWordsByIds(array_map(static fn(array $item) => $item['id'], $stmt->fetchAll()));

        shuffle($words);
        return new ArrayCollection($words);
    }

    /**
     * @param int[] $ids
     *
     * @return Word[]
     */
    public function findWordsByIds(array $ids): array
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT w
                      FROM Ig0rbm\Memo\Entity\Translation\Word w
                      WHERE w.id IN(:ids)')
            ->setParameter('ids', $ids)
            ->getResult();
    }

    public function findOneByText(string $text): ?Word
    {
        /** @var null|Word $word */
        $word = $this->findOneBy(['text' => $text]);

        return $word;
    }

    public function findWordsCollection(string $text): ?Collection
    {
        /** @var Word[] $words */
        $words = $this->findBy(['text' => $text]);
        if (empty($words)) {
            return null;
        }

        $collection = new ArrayCollection();
        foreach ($words as $word) {
            $collection->add($word);
        }

        return $collection;
    }

    /**
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function addWord(Word $word): void
    {
        $this->getEntityManager()->persist($word);
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
