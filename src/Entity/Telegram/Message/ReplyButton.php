<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Entity\Telegram\Message;

use Symfony\Component\Validator\Constraints as Assert;

class ReplyButton
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $text;

    /**
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private bool $requestLocation = false;

    /**
     * @Assert\NotBlank
     * @Assert\Type("boolean")
     */
    private bool $requestContact = false;

    public function __construct(string $text, bool $requestLocation = false, bool $requestContact = false)
    {
        $this->text            = $text;
        $this->requestLocation = $requestLocation;
        $this->requestContact  = $requestContact;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function isRequestLocation(): bool
    {
        return $this->requestLocation;
    }

    public function isRequestContact(): bool
    {
        return $this->requestContact;
    }

    public function getData()
    {
        return '/test';
    }
}
