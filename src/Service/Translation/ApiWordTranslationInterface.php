<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Translation\Direction;

interface ApiWordTranslationInterface
{
    public function getTranslate(Direction $direction, string $phrase): Collection;
}