<?php

namespace Ig0rbm\Memo\Service\Telegram\InlineKeyboard;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;

class Builder
{
    private InlineKeyboard $keyboard;

    private LineButtonsArrayValidator $buttonsArrayValidator;

    public function __construct(LineButtonsArrayValidator $buttonsArrayValidator)
    {
        $this->keyboard              = new InlineKeyboard();
        $this->buttonsArrayValidator = $buttonsArrayValidator;
    }

    public function addLine(array $buttons): self
    {
        $this->buttonsArrayValidator->validate($buttons);
        $this->keyboard->getButtonsLines()->add(new ArrayCollection($buttons));

        return $this;
    }

    public function flush(): InlineKeyboard
    {
        $keyboard = $this->keyboard;
        $this->keyboard = new InlineKeyboard();

        return $keyboard;
    }
}
