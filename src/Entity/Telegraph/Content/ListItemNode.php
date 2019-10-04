<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class ListItemNode extends AbstractElementNode
{
    public const TAG_NAME = 'li';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
