<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Exception;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Entity\Telegram\Message\UrlButton;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

use function sprintf;

class ShowAction extends AbstractTelegramAction
{
    private const WORD_LIST_URI = '/bot/%d/list';

    private WordListRepository $wordListRepository;

    private Builder $builder;

    private string $botHost;

    public function __construct(
        WordListRepository $wordListRepository,
        Builder $builder,
        string $botHost
    ) {
        $this->builder            = $builder;
        $this->wordListRepository = $wordListRepository;
        $this->botHost            = $botHost;
    }

    /**
     * @throws Exception
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText($this->translator->translate('messages.list_actions', $to->getChatId()));

        $wordList = $this->wordListRepository->findOneByChat($messageFrom->getChat());
        if ($wordList === null || $wordList->getWords()->count() === 0) {
            $to->setText($this->translator->translate('messages.list_empty', $to->getChatId()));

            return $to;
        }

        $this->builder->addLine([
            new UrlButton(
                $this->translator->translate('button.inline.show_list', $to->getChatId()),
                $this->createWordListUrl($to->getChatId())
            )
        ]);
        $this->builder->addLine([
            new InlineButton($this->translator->translate(
                'button.inline.quiz',
                $to->getChatId()),
                '/quiz_settings'
            )
        ]);

        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }

    private function createWordListUrl(int $chatId): string
    {
        return sprintf('%s'.self::WORD_LIST_URI, $this->botHost, $chatId);
    }
}
