<?php

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;

class QuizStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizStep::class);
    }
}
