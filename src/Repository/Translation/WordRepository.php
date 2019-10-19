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
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;

class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    /**
     * @throws DBALException
     */
    public function getRandomWords(string $langCode, int $limit): Collection
    {
        $conn     = $this->getConnection();
        $idsQuery = 'SELECT w.id 
                     FROM memo.public.words w
                     WHERE w.lang_code = :langCode
                     ORDER BY random()
                     LIMIT :limit';

        $stmt = $conn->prepare($idsQuery);
        $stmt->execute(['langCode' => $langCode, 'limit' => $limit]);

        $ids = array_map(
            static function (array $item) {
                return $item['id'];
            },
            $stmt->fetchAll()
        );

        $words = $this->getEntityManager()
            ->createQuery(
                'SELECT w
                      FROM Ig0rbm\Memo\Entity\Translation\Word w
                      WHERE w.id IN(:ids)')
            ->setParameter('ids', $ids)
            ->getResult();

        return new ArrayCollection($words);
    }

    public function findOneByText(string $text): ?Word
    {
        /** @var null|Word $word */
        $word = $this->findOneBy(['text' => $text]);

        return $word;
    }

    public function findWordsCollection(string $text): ?WordsBag
    {
        $words = $this->findBy(['text' => $text]);
        if (empty($words)) {
            return null;
        }

        $collection = new WordsBag();
        /** @var Word $word */
        foreach ($words as $word) {
            $collection->setWord($word);
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
