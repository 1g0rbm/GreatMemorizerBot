<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\WordList\WordListShowService;

class ShowAction extends AbstractTelegramAction
{
    /** @var WordListShowService */
    private $showService;

    public function __construct(WordListShowService $showService)
    {
        $this->showService = $showService;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $wordList = $this->showService->findByChat($messageFrom->getChat());
        $to->setText($wordList ?? $command->getTextResponse());

        return $to;
    }
}
