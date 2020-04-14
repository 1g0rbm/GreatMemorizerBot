<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Service\Quiz\QuestionBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

class QuizAction extends AbstractTelegramAction
{
    private QuizManager $quizManager;

    private QuizStepSerializer $serializer;

    private QuestionBuilder $questionBuilder;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        QuizManager $quizManager,
        QuizStepSerializer $serializer,
        QuestionBuilder $questionBuilder,
        LoggerInterface $logger
    ) {
        $this->quizManager     = $quizManager;
        $this->serializer      = $serializer;
        $this->questionBuilder = $questionBuilder;
        $this->logger = $logger;
    }

    /**
     * @throws DBALException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $this->logger->error('HERE');

        try {
            $quiz = $this->quizManager->getQuizByChat($messageFrom->getChat());
            $step = $quiz->getCurrentStep();
        } catch (QuizStepBuilderException $e) {
            $to->setText(sprintf('Error: %s', $e->getMessage()));

            return  $to;
        } catch (LicenseLimitReachedException $e) {
            $to->setText($this->translator->translate($e->getMessage(), $to->getChatId()));

            return $to;
        }

        if (!isset($step)) {
            throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
        }

        $to->setText($this->questionBuilder->build($step));
        $to->setInlineKeyboard($this->serializer->serialize($step));

        return $to;
    }
}
