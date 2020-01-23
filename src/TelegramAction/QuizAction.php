<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\QuestionBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;

class QuizAction extends AbstractTelegramAction
{
    private QuizManager $quizManager;

    private QuizStepSerializer $serializer;

    private QuestionBuilder $questionBuilder;

    public function __construct(
        QuizManager $quizManager,
        QuizStepSerializer $serializer,
        QuestionBuilder $questionBuilder
    ) {
        $this->quizManager     = $quizManager;
        $this->serializer      = $serializer;
        $this->questionBuilder = $questionBuilder;
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
            $quiz = $this->quizManager->getQuizByChat($messageFrom->getChat());
            $step = $quiz->getCurrentStep();
        } catch (QuizStepBuilderException $e) {
            $to->setText(sprintf('Error: %s', $e->getMessage()));

            return  $to;
        }

        if (!isset($step)) {
            throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
        }

        $to->setText($this->questionBuilder->build($step));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
