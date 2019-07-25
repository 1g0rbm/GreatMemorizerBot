<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Telegram\Command\CommandActionParser;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Service\Telegram\Command\CommandParser;
use Psr\Log\LoggerInterface;
use Throwable;

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

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        MessageParser $messageParser,
        CommandParser $commandParser,
        CommandActionParser $actionParser,
        TelegramApiService $telegramApiService,
        LoggerInterface $logger
    ) {
        $this->messageParser = $messageParser;
        $this->commandParser = $commandParser;
        $this->actionParser = $actionParser;
        $this->telegramApiService = $telegramApiService;
        $this->logger = $logger;
    }

    public function handle(string $raw): void
    {
        $message = $this->messageParser->createMessage($raw);
        $command = $this->defineCommand($message->getText()->getCommand());
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

    private function defineCommand(?string $command): Command
    {
        $commandsBag = $this->commandParser->createCommandCollection();
        if (!$commandsBag->has($command)) {
            return $commandsBag->get(Command::DEFAULT_COMMAND_NAME);
        }

        return $commandsBag->get($command);
    }
}