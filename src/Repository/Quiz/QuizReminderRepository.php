<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;

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
