<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Psr\Cache\InvalidArgumentException;

class QuizManager
{
    private QuizBuilder $quizBuilder;

    private QuizRepository $quizRepository;

    private LicenseLimiter $limiter;

    private AccountRepository $accountRepository;

    public function __construct(
        QuizBuilder $quizBuilder,
        QuizRepository $quizRepository,
        AccountRepository $accountRepository,
        LicenseLimiter $limiter
    ) {
        $this->quizBuilder       = $quizBuilder;
        $this->quizRepository    = $quizRepository;
        $this->accountRepository = $accountRepository;
        $this->limiter           = $limiter;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function getQuizByChat(Chat $chat, bool $withWordList = false): Quiz
    {
        $quiz = $this->quizRepository->findIncompleteQuizByChat($chat);

        if ($quiz) {
            return $quiz;
        }

        $account   = $this->accountRepository->getOneByChatId($chat->getId());
        $isReached = $this->limiter->isLimitReached(
            $account,
            'list_quiz_limit',
            new DateTimeImmutable('tomorrow midnight'),
            1
        );

        if ($isReached) {
            throw LicenseLimitReachedException::forQuiz();
        }

        return $this->quizBuilder->build($chat, $withWordList);
    }
}
