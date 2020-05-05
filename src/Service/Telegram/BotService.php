<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use DateTimeImmutable;
use Doctrine\ORM\ORMException;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\Text;
use Ig0rbm\Memo\Event\Message\CallbackQueryHandleEvent;
use Ig0rbm\Memo\Event\Telegram\BeforeParseRequestEvent;
use Ig0rbm\Memo\Event\Telegram\BeforeSendResponseEvent;
use Ig0rbm\Memo\Exception\Billing\LicenseLimitReachedException;
use Ig0rbm\Memo\Exception\PublicMessageExceptionInterface;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\Command\CommandActionParser;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

use function array_filter;
use function array_shift;
use function count;
use function preg_match;
use function sprintf;

class BotService
{
    private MessageParser $messageParser;

    private CommandParser $commandParser;

    private CommandActionParser $actionParser;

    private TelegramApiService $telegramApiService;

    private TextParser $textParser;

    private AdapterInterface $cache;

    private LoggerInterface $logger;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        MessageParser $messageParser,
        CommandParser $commandParser,
        CommandActionParser $actionParser,
        TelegramApiService $telegramApiService,
        TextParser $textParser,
        AdapterInterface $cache,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->messageParser      = $messageParser;
        $this->commandParser      = $commandParser;
        $this->actionParser       = $actionParser;
        $this->telegramApiService = $telegramApiService;
        $this->textParser         = $textParser;
        $this->cache              = $cache;
        $this->dispatcher         = $dispatcher;
        $this->logger             = $logger;
    }

    /**
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws ParseCommandException
     * @throws Throwable
     */
    public function handle(string $raw): void
    {
        $this->dispatchBeforeParseRequest($raw);

        $message     = $this->messageParser->createMessage($raw);
        $answerRoute = $this->cache->getItem(sprintf('%d_answer_route', $message->getChat()->getId()));

        if ($answerRoute->isHit()) {
            $message->getText()->setCommand($answerRoute->get());
            $answerRoute->expiresAt(new DateTimeImmutable('-5 minute'));
            $this->cache->save($answerRoute);
        }

        $command          = $this->defineCommand($message);
        $actionCollection = $this->actionParser->createActionList();

        /** @var ActionInterface $action */
        $action = $actionCollection->get($command->getActionClass());

        try {
            $response = $action->run($message, $command);
        } catch (LicenseLimitReachedException $e) {
            $response = $this->handleError(
                '/license_limit_reached',
                $e->getMessage(),
                $message,
                $actionCollection
            );
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            $translationKey = 'messages.errors.internal_error';
            if ($e instanceof PublicMessageExceptionInterface) {
                $translationKey = $e->getTranslationKey();
            }

            $response = $this->handleError(
                '/translate_and_send',
                $translationKey,
                $message,
                $actionCollection
            );
        }

        if ($response->getAnswerRoute()) {
            $answerRoute->set($response->getAnswerRoute());
            $answerRoute->expiresAt(new DateTimeImmutable('+5 minute'));
            $this->cache->save($answerRoute);
        }

        $this->dispatcher->dispatch(CallbackQueryHandleEvent::NAME, new CallbackQueryHandleEvent($message));
        $this->dispatcher->dispatch(BeforeSendResponseEvent::NAME, new BeforeSendResponseEvent($response));

        if ($response->isUpdate()) {
            $this->telegramApiService->editMessageText($message->getMessageId(), $response);
        } else {
            $this->telegramApiService->sendMessage($response);
        }
    }

    /**
     * @throws ParseCommandException
     */
    private function handleError(
        string $errorHandlerName,
        string $errorPublicMessage,
        MessageFrom $originMessageFrom,
        HandyBag $actionCollection
    ): MessageTo {
        $text = new Text();
        $text->setCommand($errorHandlerName);
        $text->setText($errorPublicMessage);

        $originMessageFrom->setText($text);
        $originMessageFrom->setCallbackQuery(null);

        $command = $this->defineCommand($originMessageFrom);
        $action  = $actionCollection->get($command->getActionClass());

        return $action->run($originMessageFrom, $command);
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
