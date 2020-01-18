<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\QuizReminder;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class QuizReminderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizReminder::class);
    }

    /**
     * @return QuizReminder[]
     */
    public function findAllEnabledByTime(string $time): array
    {
        return $this->findBy(['time' => $time, 'status' => QuizReminder::TYPE_ENABLE]);
    }

    public function findReminderByChatAndTime(Chat $chat, string $time): ?QuizReminder
    {
        /** @var QuizReminder|null $reminder */
        $reminder = $this->findOneBy(['chat' => $chat, 'time' => $time]);

        return $reminder;
    }

    /**
     * @throws ORMException
     */
    public function addQuizReminder(QuizReminder $quiz): void
    {
        $this->getEntityManager()->persist($quiz);
    }
}
