<?php

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class InlineButton
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     *
     * @var string
     */
    private $text;

    /**
     * @Assert\Type("string")
     *
     * @var string|null
     */
    private $callbackData;

    public function __construct(string $text, ?string $callbackData = null)
    {
        $this->text = $text;
        $this->callbackData = $callbackData;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getCallbackData(): string
    {
        return $this->callbackData;
    }

    /**
     * @param string $callbackData
     */
    public function setCallbackData(string $callbackData): void
    {
        $this->callbackData = $callbackData;
    }
}
