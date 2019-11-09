<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Psr\Log\LoggerInterface;

class WordRepository extends ServiceEntityRepository
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Word::class);
        $this->logger = $logger;
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

        $ids = array_map(
            static function (array $item) {
                return $item['id'];
            },
            $stmt->fetchAll()
        );

        $this->logger->critical('IDS: ', ['ids' => $ids]);

        $words = $this->getEntityManager()
            ->createQuery(
                'SELECT w
                      FROM Ig0rbm\Memo\Entity\Translation\Word w
                      WHERE w.id IN(:ids)')
            ->setParameter('ids', $ids)
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
