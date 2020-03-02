<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Paginator\Page;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Paginator\Paginator;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\WordList\WordListCleaner;

use function ceil;
use function sprintf;

class EditAction extends AbstractTelegramAction
{
    private Builder $builder;

    private WordListRepository $repository;

    private WordListCleaner $cleaner;

    private Paginator $paginator;

    public function __construct(
        Builder $builder,
        WordListRepository $repository,
        WordListCleaner $cleaner,
        Paginator $paginator
    ) {
        $this->builder    = $builder;
        $this->repository = $repository;
        $this->cleaner    = $cleaner;
        $this->paginator  = $paginator;
    }

    /**
     * @throws DBALException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $chat          = $messageFrom->getChat();
        $callbackQuery = $messageFrom->getCallbackQuery();

        $page = new Page(
            0,
            5,
            (int) ceil($this->repository->countWords($messageFrom->getChat()) / 5)
        );

        if ($callbackQuery) {
            $text = $callbackQuery->getData()->getText();

            if (strpos($text, '.')) {
                [$text, $currPage] = explode('.', $text);
            }

            if (isset($currPage)) {
                $page->setCurrentPage((int) $currPage ?? 0);
            }

            $page = $this->paginator->do($text, $page);

            if (! Paginator::isAction($text)) {
                $this->cleaner->clean($chat, $text);
            }
        }

        $wordList = $this->repository->findDistinctByChatAndLimit(
            $chat,
            $page->getPerPage(),
            $page->getOffset()
        );

        if (empty($wordList)) {
            $to->setText($command->getTextResponse());
            return $to;
        }

        foreach ($wordList as $word) {
            $this->builder->addLine([
                new InlineButton(
                    sprintf('%d . %s', $page->getCurrentItem(), $word['text']),
                    sprintf('%s.%d', $word['text'], $page->getCurrentPage())
                )
            ]);

            $page->turnToNextItem();
        }

        $this->builder->addLine(
            [
                new InlineButton(Paginator::FIRST_PAGE_ACTION, Paginator::FIRST_PAGE_ACTION),
                new InlineButton(
                    Paginator::PREV_PAGE_ACTION,
                    sprintf('%s.%d',Paginator::PREV_PAGE_ACTION, $page->getPrevPage())
                ),
                new InlineButton(
                    Paginator::NEXT_PAGE_ACTION,
                    sprintf('%s.%d', Paginator::NEXT_PAGE_ACTION, $page->getNextPage())
                ),
                new InlineButton(Paginator::LAST_PAGE_ACTION, Paginator::LAST_PAGE_ACTION),
            ]
        );

        $to->setText(
            $this->translator->translate(
                'messages.edit_list',
                $to->getChatId(),
                ['command' => $command->getCommand()]
            )
        );

        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
