<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Quiz\QuizExceptionInterface;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\AnswerChecker;
use Ig0rbm\Memo\Service\Quiz\QuestionBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Ig0rbm\Memo\Service\Quiz\ResultantService;

class QuizAnswerAction extends AbstractTelegramAction
{
    private AnswerChecker $answerChecker;

    private QuizStepSerializer $serializer;

    private ResultantService $resultant;

    private QuestionBuilder $questionBuilder;

    public function __construct(
        AnswerChecker $answerChecker,
        QuizStepSerializer $serializer,
        ResultantService $resultant,
        QuestionBuilder $questionBuilder
    ) {
        $this->answerChecker   = $answerChecker;
        $this->serializer      = $serializer;
        $this->resultant       = $resultant;
        $this->questionBuilder = $questionBuilder;
    }

    /**
     * @throws QuizStepException
     * @throws NonUniqueResultException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        try {
            $quiz = $this->answerChecker->check(
                $messageFrom->getChat(),
                $messageFrom->getCallbackQuery()->getData()->getText()
            );

            if ($quiz->isComplete()) {
                $to->setText($this->resultant->create($quiz));

                return $to;
            }
        } catch (QuizExceptionInterface $e) {
            $to->setText($e->getMessage());

            return $to;
        }

        $step = $quiz->getCurrentStep();

        $to->setText($this->questionBuilder->build($step));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
