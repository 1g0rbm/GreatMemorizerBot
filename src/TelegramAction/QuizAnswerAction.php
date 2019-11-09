<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Quiz\AnswerChecker;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;

class QuizAnswerAction extends AbstractTelegramAction
{
    /** @var AnswerChecker */
    private $answerChecker;
    /**
     * @var QuizStepSerializer
     */
    private $serializer;

    public function __construct(AnswerChecker $answerChecker, QuizStepSerializer $serializer)
    {
        $this->answerChecker = $answerChecker;
        $this->serializer    = $serializer;
    }

    /**
     * @throws QuizStepException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $step = $this->answerChecker->check(
            $messageFrom->getChat(),
            $messageFrom->getCallbackQuery()->getData()->getText()
        );

        if ($step === null) {
            $to->setText('DONE');

            return $to;
        }

        $to->setText(sprintf('What is russian for "%s"', $step->getCorrectWord()->getText()));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
