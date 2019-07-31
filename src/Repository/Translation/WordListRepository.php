<?php

namespace Ig0rbm\Memo\Repository\Translation;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ig0rbm\Memo\Entity\Translation\WordList;

class WordListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordList::class);
    }
}