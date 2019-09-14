<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;

class DirectionParser
{
    public function parse(string $rawDirection): Direction
    {
        [$langFrom, $langTo] = explode('-', $rawDirection);

        return new Direction($langFrom, $langTo);
    }
}
