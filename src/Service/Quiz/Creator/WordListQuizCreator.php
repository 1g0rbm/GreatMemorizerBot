<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz\Creator;

use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Billing\Limiter\LicenseLimiter;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Quiz\QuizStepBuilderByWordList;
use Psr\Cache\InvalidArgumentException;

class WordListQuizCreator implements QuizCreatorInterface
{
    private AccountRepository $accountRepository;

    private WordListRepository $wordListRepository;

    private QuizRepository $quizRepository;

    private LicenseLimiter $limiter;

    private QuizStepBuilderByWordList $quizStepBuilderByWordList;

    private EntityFlusher $flusher;

    public function __construct(
        AccountRepository $accountRepository,
        WordListRepository $wordListRepository,
        QuizRepository $quizRepository,
        LicenseLimiter $limiter,
        QuizStepBuilderByWordList $quizStepBuilderByWordList,
        EntityFlusher $flusher
    ) {
        $this->accountRepository         = $accountRepository;
        $this->wordListRepository        = $wordListRepository;
        $this->quizRepository            = $quizRepository;
        $this->limiter                   = $limiter;
        $this->quizStepBuilderByWordList = $quizStepBuilderByWordList;
        $this->flusher                   = $flusher;
    }

    public function type(): string
    {
        return Quiz::FROM_WORD_LIST;
    }

    /**
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws DBALException
     * @throws ORMException
     */
    public function create(Chat $chat): Quiz
    {
        $account = $this->accountRepository->getOneByChatId($chat->getId());

        $isReached = $this->limiter->isLimitReached(
            $account,
            'list_quiz_limit',
            new DateTimeImmutable('tomorrow midnight'),
            1
        );

        if ($isReached) {
            throw LicenseLimitReachedException::forQuiz();
        }

        $wordList = $this->wordListRepository->getOneByChat($chat);
        $quiz     = new Quiz();

        $quiz->setChat($chat);
        $quiz->setWordListId($wordList->getId());
        $quiz->setWordList($wordList);
        $quiz->setType(Quiz::FROM_WORD_LIST);
        $quiz->setSteps($this->quizStepBuilderByWordList->do($quiz));
        $quiz->setCurrentStep($quiz->getSteps()->first());

        $this->quizRepository->addQuiz($quiz);
        $this->flusher->flush();

        return $quiz;
    }
}
