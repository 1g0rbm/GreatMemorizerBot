<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Quiz\QuizBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;

class WordListQuizAction extends AbstractTelegramAction
{
    private QuizBuilder $quizBuilder;

    private WordListRepository $wordListRepository;

    private QuizStepSerializer $serializer;

    public function __construct(
        QuizBuilder $quizBuilder,
        WordListRepository $wordListRepository,
        QuizStepSerializer $serializer
    ) {
        $this->quizBuilder        = $quizBuilder;
        $this->wordListRepository = $wordListRepository;
        $this->serializer         = $serializer;
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

        $wordList = $this->wordListRepository->getOneByChd($messageFrom->getChat());

        $quiz = $this->quizBuilder->build($messageFrom->getChat(), $wordList->getId());
        $step = $quiz->getCurrentStep();

        if (!isset($step)) {
            throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
        }

        $to->setText(sprintf('What is russian for "%s"', $step->getCorrectWord()->getText()));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
