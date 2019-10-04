<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class BlockquoteNode extends AbstractElementNode
{
    public const TAG_NAME = 'blockquote';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
