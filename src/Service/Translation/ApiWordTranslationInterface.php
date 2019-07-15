<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Direction;

interface ApiWordTranslationInterface
{
    public function getTranslate(Direction $direction, string $phrase): HandyBag;
}