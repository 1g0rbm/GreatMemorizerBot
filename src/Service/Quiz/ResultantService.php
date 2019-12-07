<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\ResultantException;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;

use Psr\Log\LoggerInterface;
use function implode;

class ResultantService
{
    /** @var MessageBuilder */
    private $builder;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MessageBuilder $builder, LoggerInterface $logger)
    {
        $this->builder = $builder;
        $this->logger = $logger;
    }

    public function create(Quiz $quiz): string
    {
        if (!$quiz->isComplete()) {
            ResultantException::becauseQuizIsNotComplete($quiz->getId());
        }

        $steps = $quiz->getSteps()->toArray();
        usort($steps, static function (QuizStep $stepA, QuizStep $stepB) {
            return $stepA->getId() < $stepB->getId() ? -1 : 1;
        });

        $logger    = $this->logger;
        $builder   = $this->builder;
        $resultArr = array_map(static function (QuizStep $step) use ($builder, $logger) {

            $logger->critical('AAAAAAAAA', ['step' => $step->getId()]);

            /** @var Word $translation */
            $translation = $step->getCorrectWord()->getTranslations()->first();
            $builder->appendLn($step->isCorrect() ? '✅' : '❌')
                ->append('Question word: ', MessageBuilder::BOLD)
                ->appendLn($step->getCorrectWord()->getText())
                ->append('Correct translation: ', MessageBuilder::BOLD)
                ->appendLn($translation->getText());

            return $builder->flush();
        }, $steps);

        return implode(PHP_EOL, $resultArr);
    }
}
