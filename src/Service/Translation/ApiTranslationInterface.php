<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\HandyBag\HandyBag;

interface ApiTranslationInterface
{
    public function getTranslate(string $translateDirection, string $phrase): HandyBag;
}