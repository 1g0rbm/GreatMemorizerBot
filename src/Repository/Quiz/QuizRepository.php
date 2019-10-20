<?php

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * @throws ORMException
     */
    public function addQuiz(Quiz $quiz): void
    {
        $this->getEntityManager()->persist($quiz);
    }
}
