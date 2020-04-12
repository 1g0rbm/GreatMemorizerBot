<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Command\Quiz;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\Quiz\QuestionBuilder;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use Ig0rbm\Memo\Service\Telegram\TranslationService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunQuizReminderCommand extends Command
{
    private const NAME = 'memo:quiz_reminder:run';

    private TelegramApiService $telegramApi;

    private QuizReminderRepository $reminderRepository;

    private QuizManager $quizManager;

    private QuizStepSerializer $stepSerializer;

    private MessageBuilder $builder;

    private QuestionBuilder $questionBuilder;

    private TranslationService $translation;

    public function __construct(
        TelegramApiService $telegramApi,
        QuizReminderRepository $reminderRepository,
        QuizManager $quizManager,
        QuizStepSerializer $stepSerializer,
        MessageBuilder $builder,
        QuestionBuilder $questionBuilder,
        TranslationService $translation
    ) {
        parent::__construct(self::NAME);

        $this->telegramApi        = $telegramApi;
        $this->reminderRepository = $reminderRepository;
        $this->quizManager        = $quizManager;
        $this->stepSerializer     = $stepSerializer;
        $this->builder            = $builder;
        $this->questionBuilder    = $questionBuilder;
        $this->translation        = $translation;
    }

    public function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Find and run reminders by time')
            ->setHelp('The command should run by cron every minute and run reminder');
    }

    /**
     * @throws DBALException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '=========',
            '  START  ',
            '========='
        ]);

        $dt        = new DateTime('now', new DateTimeZone('UTC'));
        $reminders = $this->reminderRepository->findAllEnabledByTime($dt->format('H:i'));

        $output->writeln([sprintf('Found reminders for run: %d', count($reminders))]);

        foreach ($reminders as $reminder) {
            try {
                $quiz = $this->quizManager->getQuizByChat($reminder->getChat(), true);
                $step = $quiz->getCurrentStep();
            } catch (LicenseLimitReachedException $e) {
                echo 'CONTINUE';
                continue;
            }

            if (!isset($step)) {
                throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
            }

            $this->builder->appendLn(
                $this->translation->translate('messages.reminder_greetings', $reminder->getChat()->getId())
            )
                ->appendLn('')
                ->appendLn($this->questionBuilder->build($step));

            $to = new MessageTo();
            $to->setChatId($reminder->getChat()->getId());
            $to->setText($this->builder->flush());
            $to->setInlineKeyboard($this->stepSerializer->serialize($step));

            $this->telegramApi->sendMessage($to);

            $output->writeln([sprintf('Send quiz for chat %d', $reminder->getChat()->getId())]);
        }

        $output->writeln([
            '=========',
            '   END   ',
            '========='
        ]);

        return 0;
    }
}
