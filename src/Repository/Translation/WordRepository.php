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

use function array_combine;
use function array_map;
use function implode;
use function shuffle;

class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    /**
     * @param string[] $pos
     * @param int[] $exclude
     *
     * @return Collection|Word[]
     *
     * @throws DBALException
     */
    public function getRandomWordsByWordListId(
        string $langCode,
        array $pos,
        int $wordListId,
        int $limit,
        array $exclude = []
    ): Collection {
        $conn           = $this->getConnection();
        $posPlaceHolder = array_map(fn(string $item) => ':' . $item, $pos);
        $pos            = array_combine($posPlaceHolder, $pos);
        $posPlaceHolder = implode(', ', $posPlaceHolder);
        $excludeSql     = empty($exclude) ? '' : sprintf('AND w.id NOT IN (%s)', implode(', ', $exclude));

        $idsQuery = "SELECT DISTINCT ON(text) id, text
                     FROM (
                         SELECT * FROM memo.public.words w
                             JOIN lists2words l2w on w.id = l2w.word_id and l2w.word_list_id = :wordListId
                         WHERE w.lang_code = :langCode
                             AND w.pos IN({$posPlaceHolder})
                             $excludeSql
                         ORDER BY random()
                         LIMIT :limit
                     ) t1";

        $stmt = $conn->prepare($idsQuery);
        $stmt->execute(array_merge([
            'langCode'   => $langCode,
            'wordListId' => $wordListId,
            'limit'      => $limit
        ], $pos));

        $words = $this->findWordsByIds(array_map(static fn(array $item) => $item['id'], $stmt->fetchAll()));

        shuffle($words);
        return new ArrayCollection($words);
    }

    /**
     * @param string[] $pos
     * @param int[]    $exclude
     *
     * @return ArrayCollection|Word[]
     *
     * @throws DBALException
     */
    public function getRandomWords(string $langCode, array $pos, int $limit, array $exclude = []): ArrayCollection
    {
        $conn               = $this->getConnection();
        $posPlaceHolder     = array_map(fn(string $item) => ':' . $item, $pos);
        $pos                = array_combine($posPlaceHolder, $pos);
        $posPlaceHolder     = implode(', ', $posPlaceHolder);
        $excludeSql     = empty($exclude) ? '' : sprintf('AND w.id NOT IN (%s)', implode(', ', $exclude));

        $idsQuery = "SELECT DISTINCT ON(text) id, text 
                     FROM (
                         SELECT * FROM memo.public.words w
                         WHERE w.lang_code = :langCode
                             AND w.pos IN ($posPlaceHolder)
                             $excludeSql
                         ORDER BY random()
                         LIMIT :limit
                     ) t1";

        $stmt = $conn->prepare($idsQuery);
        $stmt->execute(array_merge([
            'langCode' => $langCode,
            'limit' => $limit
        ], $pos));

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

    /**
     * @return Word[]|Collection|null
     */
    public function findWordsCollection(string $text): ?Collection
    {
        $qb = $this->createQueryBuilder('w')
            ->join('w.translations', 'wt')
            ->where('w.text = :text')
            ->orderBy('w.id', 'ASC')
            ->setParameter('text', $text);

        $words = $qb->getQuery()->getResult();
        if (empty($words)) {
            return null;
        }

        return new ArrayCollection($words);
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
