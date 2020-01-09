<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\AnswerCallbackQuery;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Event\Message\CallbackQueryHandleEvent;
use Ig0rbm\Memo\Exception\Quiz\QuizExceptionInterface;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WordListQuizAction extends AbstractTelegramAction
{
    private QuizManager $quizManager;

    private QuizStepSerializer $serializer;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        QuizManager $quizManager,
        QuizStepSerializer $serializer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->quizManager = $quizManager;
        $this->serializer  = $serializer;
        $this->dispatcher  = $dispatcher;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws QuizStepException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        try {
            $quiz = $this->quizManager->getQuizByChat($messageFrom->getChat(), true);
            $step = $quiz->getCurrentStep();
        } catch (QuizExceptionInterface $e) {
            $to->setText($e->getMessage());

            return $to;
        }

        if (!isset($step)) {
            throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
        }

        $to->setText(sprintf('What is russian for "%s"', $step->getCorrectWord()->getText()));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        $callbackAnswer = new AnswerCallbackQuery();
        $callbackAnswer->setCallbackQueryId($messageFrom->getCallbackQuery()->getId());

        $this->dispatcher->dispatch(
            CallbackQueryHandleEvent::NAME,
            new CallbackQueryHandleEvent($callbackAnswer)
        );

        return $to;
    }
}
