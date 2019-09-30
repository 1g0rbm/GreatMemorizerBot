<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class BrNode extends AbstractElementNode
{
    public const TAG_NAME = 'br';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
