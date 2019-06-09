<?php


namespace Ig0rbm\Memo\Service\Telegram;

/**
 * @package Ig0rbm\Memo\Service\Telegram
 */
class TokenChecker
{
    /**
     * @var string
     */
    private $telegramSecretKey;

    public function __construct(string $telegramSecretKey)
    {
        $this->telegramSecretKey = $telegramSecretKey;
    }

    public function isValidToken(string $token): bool
    {
        return $this->telegramSecretKey === $token;
    }
}