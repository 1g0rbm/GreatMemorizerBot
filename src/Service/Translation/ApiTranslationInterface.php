<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\Direction;

interface ApiTranslationInterface
{
    public function getTranslate(Direction $direction, string $phrase): Word;
}