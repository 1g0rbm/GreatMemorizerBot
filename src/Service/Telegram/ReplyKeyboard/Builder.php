<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Telegram\ReplyKeyboard;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Entity\Telegram\Message\ReplyKeyboard;

class Builder
{
    private ReplyKeyboard $keyboard;

    public function __construct()
    {
        $this->keyboard = new ReplyKeyboard();
    }

    public function addLine(array $buttons)
    {
        $this->keyboard->getButtonsLines()->add(new ArrayCollection($buttons));
    }

    public function flush(): ReplyKeyboard
    {
        $keyboard = $this->keyboard;
        $this->keyboard = new ReplyKeyboard();

        return $keyboard;
    }
}
