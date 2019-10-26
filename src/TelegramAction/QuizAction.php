<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Psr\Log\LoggerInterface;

class QuizAction extends AbstractTelegramAction
{
    /** @var QuizManager */
    private $quizManager;

    /** @var QuizStepSerializer */
    private $serializer;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        QuizManager $quizManager,
        QuizStepSerializer $serializer,
        LoggerInterface $logger
    ) {
        $this->quizManager = $quizManager;
        $this->serializer  = $serializer;
        $this->logger      = $logger;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText('Quiz will be here');

        $quiz = $this->quizManager->getQuizByChat($messageFrom->getChat());

        foreach ($quiz->getSteps() as $step) {
            if (false === $step->isAnswered()) {
                break;
            }
        }

        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
