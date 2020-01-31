<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram;

use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Repository\AccountRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationService
{

    private TranslatorInterface $translator;

    private AccountRepository $accountRepository;

    public function __construct(TranslatorInterface $translator, AccountRepository $accountRepository)
    {
        $this->translator        = $translator;
        $this->accountRepository = $accountRepository;
    }

    public function translate(string $id, int $chatId, array $params = []): string
    {
        $account = $this->accountRepository->getOneByChatId($chatId);

        return $this->translator->trans($id, $params, null, $account->getLocale());
    }
}
