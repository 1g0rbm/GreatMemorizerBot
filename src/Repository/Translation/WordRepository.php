<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\Memo\Entity\Translation\Word;

use function array_map;

class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
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

        $words = $this->getEntityManager()
            ->createQuery(
                'SELECT w
                      FROM Ig0rbm\Memo\Entity\Translation\Word w
                      WHERE w.id IN(:ids)')
            ->setParameter('ids', array_map(static fn(array $item) => $item['id'], $stmt->fetchAll()))
            ->getResult();

        shuffle($words);
        return new ArrayCollection($words);
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
