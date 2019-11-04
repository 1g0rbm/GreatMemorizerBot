<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Throwable;
use Doctrine\ORM\ORMException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Event\Telegram\BeforeParseRequestEvent;
use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\Command\CommandActionParser;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Ig0rbm\Memo\Exception\Telegram\Command\ParseCommandException;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Psr\Log\LoggerInterface;

class BotService
{
    /** @var MessageParser */
    private $messageParser;

    /** @var CommandParser */
    private $commandParser;

    /** @var CommandActionParser */
    private $actionParser;

    /** @var TelegramApiService */
    private $telegramApiService;

    /** @var TextParser */
    private $textParser;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(
        MessageParser $messageParser,
        CommandParser $commandParser,
        CommandActionParser $actionParser,
        TelegramApiService $telegramApiService,
        TextParser $textParser,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger
    ) {
        $this->messageParser      = $messageParser;
        $this->commandParser      = $commandParser;
        $this->actionParser       = $actionParser;
        $this->telegramApiService = $telegramApiService;
        $this->textParser         = $textParser;
        $this->dispatcher         = $dispatcher;
        $this->logger             = $logger;
    }

    /**
     * @throws ORMException
     * @throws ParseCommandException
     */
    public function handle(string $raw): void
    {
        $this->dispatchBeforeParseRequest($raw);

        $message = $this->messageParser->createMessage($raw);

        $command = $this->defineCommand($message);
        $actionCollection = $this->actionParser->createActionList();

        /** @var ActionInterface $action */
        $action = $actionCollection->get($command->getActionClass());

        try {
            $response = $action->run($message, $command);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            $response = new MessageTo();
            $response->setChatId($message->getChat()->getId());
            $response->setText(sprintf('Error during handle message "%s"', $message->getText()->getText()));
        }

        $this->telegramApiService->sendMessage($response);
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

        $command = $command ?? $from->getText()->getCommand();

        $commandsBag = $this->commandParser->createCommandCollection();
        if (!$commandsBag->has($command)) {
            return $commandsBag->get(Command::DEFAULT_COMMAND_NAME);
        }

        return $commandsBag->get($command);
    }
}
