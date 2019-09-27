<?php

namespace Ig0rbm\Memo\Entity\Telegraph\Content;;

use JsonSerializable;

abstract class AbstractElementNode implements JsonSerializable
{
    /** @var string */
    protected $tag;

    /** @var array */
    protected $attrs = [];

    /** @var array */
    protected $children = [];

    public function toArray(): array
    {
        $result = [
            'tag' => $this->tag
        ];

        if (!empty($this->attrs)) {
            $result['attrs'] = $this->attrs;
        }

        if (!empty($this->children)) {
            $result['children'] = $this->children;
        }

        return $result;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
