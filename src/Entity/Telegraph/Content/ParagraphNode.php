<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

class ParagraphNode extends AbstractElementNode
{
    public const TAG_NAME = 'p';

    public function __construct()
    {
        $this->tag = self::TAG_NAME;
    }

    public function setText(string $text): void
    {
        $this->children = [$text];
    }

    public function addChild(AbstractElementNode $node): void
    {
        $this->children[] = $node;
    }
}
