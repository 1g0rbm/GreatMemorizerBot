<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

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

    /**
     * @throws DBALException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $wordList = $this->repository->findDistinctByChat($messageFrom->getChat());
        if (empty($wordList)) {
            $to->setText($command->getTextResponse());
            return $to;
        }

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

        $to->setText(sprintf('%s press button to delete word', $command->getCommand()));
        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
