<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\ORM\ORMException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class QuizReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizReminder::class);
    }

    /**
     * @throws ORMException
     */
    public function addQuizReminder(QuizReminder $quiz): void
    {
        $this->getEntityManager()->persist($quiz);
    }
}
