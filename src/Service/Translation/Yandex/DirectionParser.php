<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Ig0rbm\Memo\Entity\Translation\Yandex\Direction;

class DirectionParser
{
    public function parse(string $rawDirection): Direction
    {
        [$langFrom, $langTo] = explode('-', $rawDirection);

        $direction = new Direction();
        $direction->setDirection($rawDirection);
        $direction->setLangFrom($langFrom);
        $direction->setLangTo($langTo);

        return $direction;
    }
}