<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;

interface ApiTextTranslationInterface
{
    public function getTranslate(Direction $direction, string $phrase): Text;
}