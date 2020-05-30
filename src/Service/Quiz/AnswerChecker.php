<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class AnswerChecker
{
    private QuizRepository $quizRepository;

    private Rotator $rotator;

    private EntityFlusher $flusher;

    public function __construct(QuizRepository $quizRepository, Rotator $rotator, EntityFlusher $flusher)
    {
        $this->quizRepository = $quizRepository;
        $this->rotator        = $rotator;
        $this->flusher        = $flusher;
    }

    /**
     * @throws QuizStepException
     * @throws NonUniqueResultException
     */
    public function check(Chat $chat, Text $text): Quiz
    {
        $quiz = $this->quizRepository->getIncompleteQuizByChat($chat);
        $step = $quiz->getCurrentStep();

        if ($step->isAnswered()) {
            throw QuizStepException::becauseThereAreNotUnansweredSteps($quiz->getId());
        }

        $this->do($step, (int) $text->getParameters()->get('w'));

        $step = $this->rotator->rotate($step->getQuiz());
        $step === null ? $quiz->setIsComplete(true) : $quiz->setCurrentStep($step);

        $this->flusher->flush();

        return $quiz;
    }

    private function do(QuizStep $step, int $answerWordId): QuizStep
    {
        $step->setIsCorrect($this->isCorrect($step, $answerWordId));
        $step->setIsAnswered(true);
        $step->setAnswerWord($this->extractUserWord($step, $answerWordId));

        return $step;
    }

    private function extractUserWord(QuizStep $step, int $answerWordId): Word
    {
        /** @var Word $word */
        $word = $step->getWords()
            ->filter(
                static function (Word $word) use ($answerWordId) {
                    return $word->getTranslations()
                        ->filter(static fn(Word $word) => $word->getId() === $answerWordId)
                        ->first();
                }
            )
            ->first();

        return $word->getTranslations()->first();
    }

    private function isCorrect(QuizStep $step, int $answerWordId): bool
    {
        return $step->getCorrectWord()
            ->getTranslations()
            ->exists(static fn(int $key, Word $word) => $word->getId() === $answerWordId);
    }
}
