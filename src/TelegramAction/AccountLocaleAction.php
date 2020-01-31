<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Repository\AccountRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

class AccountLocaleAction extends AbstractTelegramAction
{
    private Builder $builder;

    private AccountRepository $accountRepository;

    private EntityFlusher $flusher;

    public function __construct(Builder $builder, AccountRepository $accountRepository, EntityFlusher $flusher)
    {
        $this->builder           = $builder;
        $this->accountRepository = $accountRepository;
        $this->flusher           = $flusher;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());
        $to->setText($command->getTextResponse());

        if ($messageFrom->getCallbackQuery()) {
            $account = $this->accountRepository->getOneByChatId($to->getChatId());

            $account->setLocale($messageFrom->getCallbackQuery()->getData()->getText());
            $account->setNeedKeyboardUpdate(true);

            $this->flusher->flush();

            $to->setText($this->translator->translate(
                'messages.change_language',
                $to->getChatId(),
                ['language' => $account->getLocale()]
            ));

            return $to;
        }

        $this->builder->addLine([
            new InlineButton('🇷🇺 ru', 'ru'),
            new InlineButton('🇬🇧 en', 'en')
        ]);

        $to->setInlineKeyboard($this->builder->flush());

        return $to;
    }
}
