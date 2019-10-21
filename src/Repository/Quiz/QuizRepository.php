<?php

namespace Ig0rbm\Memo\Repository\Quiz;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findIncompleteQuizByChat(Chat $chat): ?Quiz
    {
        /** @var Quiz|null $quiz */
        $quiz = $this->findOneBy(['chat' => $chat, 'isComplete' => false]);

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
