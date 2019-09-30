<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class ItalicNode extends AbstractElementNode
{
    public const TAG_NAME = 'i';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
