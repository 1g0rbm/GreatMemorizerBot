<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class AnswerCallbackQuery
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $callbackQueryId;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private ?string $text = null;

    /**
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private bool $showAlert = false;

    /**
     * @Assert\Type("string")
     */
    private ?string $url = null;

    /**
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private int $cacheTime = 0;

    public function getCallbackQueryId(): int
    {
        return $this->callbackQueryId;
    }

    public function setCallbackQueryId(int $callbackQueryId): void
    {
        $this->callbackQueryId = $callbackQueryId;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function isShowAlert(): bool
    {
        return $this->showAlert;
    }

    public function setShowAlert(bool $showAlert): void
    {
        $this->showAlert = $showAlert;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getCacheTime(): int
    {
        return $this->cacheTime;
    }

    public function setCacheTime(int $cacheTime): void
    {
        $this->cacheTime = $cacheTime;
    }
}
