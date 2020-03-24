<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Repository\Quiz\QuizRepository;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;

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
    public function check(Chat $chat, string $answer): Quiz
    {
        $quiz = $this->quizRepository->getIncompleteQuizByChat($chat);
        $step = $quiz->getCurrentStep();

        if ($step->isAnswered()) {
            throw QuizStepException::becauseThereAreNotUnansweredSteps($quiz->getId());
        }

        $this->do($step, $answer);

        $step = $this->rotator->rotate($step->getQuiz());
        $step === null ? $quiz->setIsComplete(true) : $quiz->setCurrentStep($step);

        $this->flusher->flush();

        return $quiz;
    }

    private function do(QuizStep $step, string $answer): QuizStep
    {
        $answer = $step->getCorrectWord()->getTranslations()->filter(fn (Word $word) => $word->getText() === $answer);
        $step->setIsCorrect($answer->count() === 1);
        $step->setIsAnswered(true);

        return $step;
    }
}
