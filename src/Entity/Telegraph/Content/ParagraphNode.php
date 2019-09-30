<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class ParagraphNode extends AbstractElementNode
{
    public const TAG_NAME = 'p';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
