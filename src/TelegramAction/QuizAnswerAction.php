<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\NonUniqueResultException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Quiz\AnswerChecker;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Ig0rbm\Memo\Service\Quiz\ResultantService;

class QuizAnswerAction extends AbstractTelegramAction
{
    /** @var AnswerChecker */
    private $answerChecker;

    /** @var QuizStepSerializer */
    private $serializer;

    /** @var ResultantService */
    private $resultant;

    public function __construct(
        AnswerChecker $answerChecker,
        QuizStepSerializer $serializer,
        ResultantService $resultant
    ) {
        $this->answerChecker = $answerChecker;
        $this->serializer    = $serializer;
        $this->resultant     = $resultant;
    }

    /**
     * @throws QuizStepException
     * @throws NonUniqueResultException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $quiz = $this->answerChecker->check(
            $messageFrom->getChat(),
            $messageFrom->getCallbackQuery()->getData()->getText()
        );

        if ($quiz->isComplete()) {
            $to->setText($this->resultant->create($quiz));

            return $to;
        }

        $step = $quiz->getCurrentStep();
        $to->setText(sprintf('What is russian for "%s"', $step->getCorrectWord()->getText()));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
