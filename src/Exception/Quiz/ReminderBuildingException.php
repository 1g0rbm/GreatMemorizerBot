<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\Quiz;

use Ig0rbm\Memo\Exception\PublicMessageExceptionInterface;
use RuntimeException;
use Throwable;

class ReminderBuildingException extends RuntimeException implements PublicMessageExceptionInterface
{
    private string $translationKey;

    public function __construct(string $message, string $translationKey)
    {
        $this->translationKey = $translationKey;

        parent::__construct($message, 400, null);
    }

    public static function becauseThereIsNoTimeZoneForChat(int $chatId): self
    {
        return new self(
            sprintf('There is no TimeZone for chat with id %d', $chatId),
            'messages.errors.reminder.timezone_not_found'
        );
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }
}
