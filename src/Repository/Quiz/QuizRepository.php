<?php

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Quiz\QuizException;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getIncompleteQuizByChat(Chat $chat): Quiz
    {
        $quiz = $this->findIncompleteQuizByChat($chat);
        if (!$quiz) {
            throw QuizException::becauseThereIsNoQuizForChat($chat->getId());
        }

        return $quiz;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findIncompleteQuizByChat(Chat $chat): ?Quiz
    {
        /** @var Quiz|null $quiz */
        $quiz = $this->createQueryBuilder('c')
            ->leftJoin('c.steps', 'cs')
            ->where('c.chat = :chat')
            ->andWhere('c.isComplete = :isComplete')
            ->setParameters(['chat' => $chat, 'isComplete' => false])
            ->getQuery()
            ->getOneOrNullResult();

        return $quiz;
    }

    /**
     * @throws ORMException
     */
    public function addQuiz(Quiz $quiz): void
    {
        $this->getEntityManager()->persist($quiz);
    }
}
