<?php

namespace Ig0rbm\Memo\Service\Translation;

class TranslationService
{
    /** @var ApiTranslationInterface */
    private $apiTranslation;

    public function __construct(ApiTranslationInterface $apiTranslation)
    {
        $this->apiTranslation = $apiTranslation;
    }
}