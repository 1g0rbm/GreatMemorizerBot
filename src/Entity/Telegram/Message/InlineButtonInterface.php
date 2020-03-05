<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

interface InlineButtonInterface
{
    /**
     * Return button's label text
     */
    public function getText(): string;

    /**
     * Return button's callback data
     */
    public function getCallbackData(): ?string;

    /**
     * Return button's key name in telegram response
     */
    public function getKey(): string;
}
