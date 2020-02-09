<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Exception;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Ig0rbm\Memo\Service\Telegram\TranslationMessageBuilder;
use Ig0rbm\Memo\Service\WordList\WordListShowService;

class ShowAction extends AbstractTelegramAction
{
    private WordListRepository $wordListRepository;

    private TranslationMessageBuilder $translationMessageBuilder;

    private Builder $builder;

    public function __construct(
        WordListRepository $wordListRepository,
        TranslationMessageBuilder $translationMessageBuilder,
        Builder $builder
    ) {
        $this->builder                   = $builder;
        $this->wordListRepository        = $wordListRepository;
        $this->translationMessageBuilder = $translationMessageBuilder;
    }

    /**
     * @throws Exception
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $wordList = $this->wordListRepository->getOneByChat($messageFrom->getChat());

        $to->setText($this->translationMessageBuilder->buildFromWords($wordList->getWords()));

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
}
