<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class UrlButton implements InlineButtonInterface
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $text;

    /**
     * @Assert\Type("string")
     */
    private ?string $callbackData = null;

    /**
     * @Assert\Type("string")
     */
    private string $keyName = 'url';

    public function __construct(string $text, ?string $callbackData = null)
    {
        $this->text         = $text;
        $this->callbackData = $callbackData;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCallbackData(): string
    {
        return $this->callbackData;
    }

    public function setCallbackData(string $callbackData): void
    {
        $this->callbackData = $callbackData;
    }

    public function getKey(): string
    {
        return $this->keyName;
    }
}
