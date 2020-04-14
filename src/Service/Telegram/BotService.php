<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use Doctrine\ORM\ORMException;
use Exception;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Event\Message\CallbackQueryHandleEvent;
use Ig0rbm\Memo\Event\Telegram\BeforeParseRequestEvent;
use Ig0rbm\Memo\Event\Telegram\BeforeSendResponseEvent;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\Command\CommandActionParser;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

use function array_filter;
use function array_shift;
use function count;
use function preg_match;

class BotService
{
    private MessageParser $messageParser;

    private CommandParser $commandParser;

    private CommandActionParser $actionParser;

    private TelegramApiService $telegramApiService;

    private TextParser $textParser;

    private TranslationService $translationService;

    private LoggerInterface $logger;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        MessageParser $messageParser,
        CommandParser $commandParser,
        CommandActionParser $actionParser,
        TelegramApiService $telegramApiService,
        TextParser $textParser,
        TranslationService $translationService,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->messageParser      = $messageParser;
        $this->commandParser      = $commandParser;
        $this->actionParser       = $actionParser;
        $this->telegramApiService = $telegramApiService;
        $this->textParser         = $textParser;
        $this->translationService = $translationService;
        $this->dispatcher         = $dispatcher;
        $this->logger             = $logger;
    }

    /**
     * @throws ORMException
     * @throws ParseCommandException
     * @throws Exception
     */
    public function handle(string $raw): void
    {
        $this->dispatchBeforeParseRequest($raw);

        $message          = $this->messageParser->createMessage($raw);
        $command          = $this->defineCommand($message);
        $actionCollection = $this->actionParser->createActionList();

        /** @var ActionInterface $action */
        $action = $actionCollection->get($command->getActionClass());

        try {
            $response = $action->run($message, $command);
        } catch (LicenseLimitReachedException $e) {
            $text = new Text();
            $text->setText($e->getTranslationLabel());
            $text->setCommand('/license_limit_handle');

            $command = $this->defineCommand($message);

            $message->setCallbackQuery(null);
            $message->setText($text);

            /** @var ActionInterface $action */
            $action = $actionCollection->get($command->getActionClass());

            $response = $action->run($message, $command);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            $response = new MessageTo();
            $response->setChatId($message->getChat()->getId());
            $response->setText(sprintf('Error during handle message "%s"', $message->getText()->getText()));
        }

        $this->dispatcher->dispatch(CallbackQueryHandleEvent::NAME, new CallbackQueryHandleEvent($message));
        $this->dispatcher->dispatch(BeforeSendResponseEvent::NAME, new BeforeSendResponseEvent($response));

        $this->logger->error('UPD', ['upd' => (int) $response->isUpdate()]);

        if ($response->isUpdate()) {
            $this->telegramApiService->editMessageText($message->getMessageId(), $response);
        } else {
            $this->telegramApiService->sendMessage($response);
        }
    }

    private function dispatchBeforeParseRequest(string $message): void
    {
        $this->dispatcher->dispatch(BeforeParseRequestEvent::NAME, new BeforeParseRequestEvent($message));
    }

    /**
     * @throws ParseCommandException
     */
    private function defineCommand(MessageFrom $from): Command
    {
        $callback = $from->getCallbackQuery();
        if ($callback) {
            $command = $callback->getData()->getCommand();
        }

        $text = $from->getText() ?? $from->getReply()->getText();

        $command = $command ?? $text->getCommand();

        $commandsBag = $this->commandParser->createCommandCollection();
        if ($commandsBag->has($command)) {
            return $commandsBag->get($command);
        }

        $commands = array_filter(
            $commandsBag->getAll(),
            static function (Command $command) use ($text) {
                $aliases = $command->getAliases()->filter(static function (string $alias) use ($text) {
                    return preg_match('#' . $alias . '#', $text->getText()) === 1;
                });

                return $aliases->count() > 0;
            }
        );

        return count($commands) > 0 ?
            array_shift($commands) :
            $commandsBag->get(Command::DEFAULT_COMMAND_NAME);
    }
}
