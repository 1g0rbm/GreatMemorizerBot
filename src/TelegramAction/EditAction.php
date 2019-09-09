<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Psr\Log\LoggerInterface;

class EditAction extends AbstractTelegramAction
{
    /** @var Builder */
    private $builder;

    /** @var WordListRepository */
    private $repository;

    public function __construct(Builder $builder, WordListRepository $repository)
    {
        $this->builder = $builder;
        $this->repository = $repository;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $wordList = $this->repository->findDistinctByChat($messageFrom->getChat());

        foreach ($wordList as $word) {
            $this->builder->addLine(
                [
                    new InlineButton(
                        $word['text'],
                        sprintf('delete:%s', $word['text'])
                    ),
                ]
            );
        }

        $to->setInlineKeyboard($this->builder->flush());

        $to->setText($command->getTextResponse());

        return $to;
    }
}
