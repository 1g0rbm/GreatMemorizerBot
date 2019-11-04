<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Psr\Log\LoggerInterface;

class QuizAnswerAction extends AbstractTelegramAction
{
    /** @var QuizManager */
    private $quizManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(QuizManager $quizManager, LoggerInterface $logger)
    {
        $this->quizManager = $quizManager;
        $this->logger = $logger;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $quiz = $this->quizManager->getQuizByChat($messageFrom->getChat());

        foreach ($quiz->getSteps() as $step) {
            if (false === $step->isAnswered()) {
                break;
            }
        }

        /** @var Word $correctTranslation */
        $correctTranslation = $step->getCorrectWord()->getTranslations()->first();

        $this->logger->critical(
            'ANSWER',
            ['correct' => $correctTranslation->getText(), 'answer' => $messageFrom->getCallbackQuery()->getData()->getText()]
        );

        if ($correctTranslation->getText() === $messageFrom->getCallbackQuery()->getData()->getText()) {
            $to->setText('right');

            return $to;
        }

        $to->setText('mistake');

        return $to;
    }
}
