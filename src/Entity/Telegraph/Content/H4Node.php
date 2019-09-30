<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class H4Node extends AbstractElementNode
{
    public const TAG_NAME = 'h4';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
