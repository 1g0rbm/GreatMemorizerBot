<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Word;

class TranslationService
{
    /** @var ApiTranslationInterface */
    private $apiTranslation;

    public function __construct(ApiTranslationInterface $apiTranslation)
    {
        $this->apiTranslation = $apiTranslation;
    }

    public function translate(string $translateDirection, string $word): Word
    {
        return $this->apiTranslation->getTranslate($translateDirection, $word);
    }
}