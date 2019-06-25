<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Word;

interface ApiTranslationInterface
{
    public function getTranslate(string $translateDirection, string $phrase): Word;
}