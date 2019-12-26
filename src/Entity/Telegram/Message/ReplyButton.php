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

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
