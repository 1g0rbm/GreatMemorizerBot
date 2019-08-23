<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;

class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
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
}