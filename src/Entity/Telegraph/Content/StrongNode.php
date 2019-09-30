<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class StrongNode extends AbstractElementNode
{
    public const TAG_NAME = 'strong';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
