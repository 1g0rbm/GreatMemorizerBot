<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Command\Quiz;

use DateTime;
use DateTimeZone;
use Exception;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Quiz\QuizStepException;
use Ig0rbm\Memo\Repository\Quiz\QuizReminderRepository;
use Ig0rbm\Memo\Service\Quiz\QuizManager;
use Ig0rbm\Memo\Service\Quiz\QuizStepSerializer;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
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

    public function __construct(
        TelegramApiService $telegramApi,
        QuizReminderRepository $reminderRepository,
        QuizManager $quizManager,
        QuizStepSerializer $stepSerializer,
        MessageBuilder $builder
    ) {
        parent::__construct(self::NAME);

        $this->telegramApi        = $telegramApi;
        $this->reminderRepository = $reminderRepository;
        $this->quizManager        = $quizManager;
        $this->stepSerializer     = $stepSerializer;
        $this->builder            = $builder;
    }

    public function configure(): void
    {
        $this->setName(self::NAME)
            ->setDescription('Find and run reminders by time')
            ->setHelp('The command should run by cron every minute and run reminder');
    }

    /**
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
            $quiz = $this->quizManager->getQuizByChat($reminder->getChat(), true);
            $step = $quiz->getCurrentStep();

            if (!isset($step)) {
                throw QuizStepException::becauseThereAreNotQuizSteps($quiz->getId());
            }

            $text = sprintf(
                'What is russian for "%s" and pos "%s"?',
                $step->getCorrectWord()->getText(),
                $step->getCorrectWord()->getPos()
            );

            $this->builder->appendLn('🤖 Hi! Time to remember English!')->appendLn('')->appendLn($text);

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
    }
}
