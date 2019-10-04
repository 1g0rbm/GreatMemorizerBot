<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;

class ListNode extends AbstractElementNode
{
    public const TAG_NAME = 'ul';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }
}
