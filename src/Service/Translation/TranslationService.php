<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\HandyBag\HandyBag;

class TranslationService
{
    /** @var ApiTranslationInterface */
    private $apiTranslation;

    /** @var DirectionParser */
    private $directionParser;

    public function __construct(ApiTranslationInterface $apiTranslation, DirectionParser $directionParser)
    {
        $this->apiTranslation = $apiTranslation;
        $this->directionParser = $directionParser;
    }

    public function translate(string $translateDirection, string $word): HandyBag
    {
        return $this->apiTranslation->getTranslate($this->directionParser->parse($translateDirection), $word);
    }
}