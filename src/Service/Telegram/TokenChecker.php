<?php

namespace Ig0rbm\Memo\Service\Telegram;

class TokenChecker
{
    private string $telegramSecretKey;

    public function __construct(string $telegramSecretKey)
    {
        $this->telegramSecretKey = $telegramSecretKey;
    }

    public function isValidToken(string $token): bool
    {
        return $this->telegramSecretKey === $token;
    }
}
