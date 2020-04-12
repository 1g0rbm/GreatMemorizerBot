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

    public function countRemindersForChat(Chat $chat): int
    {
        return $this->count(['chat' => $chat]);
    }

    public function deleteReminderByChatAndTime(Chat $chat, string $time): void
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('Ig0rbm\Memo\Entity\Quiz\QuizReminder', 'qr')
            ->where('qr.chat = :chat')
            ->andWhere('qr.time = :time')
            ->setParameters([
                ':chat' => $chat,
                ':time' => $time,
            ]);

        $qb->getQuery()->execute();
    }

    /**
     * @return QuizReminder[]
     */
    public function findAllRemindersByChat(Chat $chat): array
    {
        return $this->findBy(['chat' => $chat]);
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
