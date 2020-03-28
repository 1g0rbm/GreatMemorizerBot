<?php

namespace Ig0rbm\Memo\Command\ReplyKeyboard;

use Ig0rbm\Memo\Entity\Telegram\Keyboard\ReplyKeyboardRemove;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReplyKeyboardRemoveCommand extends Command
{
    protected static $defaultName = 'memo:reply-keyboard:remove';

    private TelegramApiService $telegramApi;

    private ChatRepository $chatRepository;

    public function __construct(TelegramApiService $telegramApi, ChatRepository $chatRepository)
    {
        parent::__construct();

        $this->telegramApi    = $telegramApi;
        $this->chatRepository = $chatRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Remove telegram ReplyKeyboard')
            ->setHelp('The command allows you to remove ReplyKeyboard from telegram bot');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $chats = $this->chatRepository->findAll();

        foreach ($chats as $chat) {
            $to = new MessageTo();
            $to->setChatId($chat->getId());
            $to->setText('ReplyKeyboard was removed');
            $to->setReplyKeyboardRemove(new ReplyKeyboardRemove());

            $this->telegramApi->sendMessage($to);
        }

        return 0;
    }
}
