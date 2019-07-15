<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Text;

class TextTranslationService
{
    /** @var ApiTextTranslationInterface */
    private $apiTranslation;

    /** @var DirectionParser */
    private $directionParser;

    public function __construct(ApiTextTranslationInterface $apiTranslation, DirectionParser $directionParser)
    {
        $this->apiTranslation = $apiTranslation;
        $this->directionParser = $directionParser;
    }

    public function translate(string $translateDirection, string $text): Text
    {
        return $this->apiTranslation->getTranslate($this->directionParser->parse($translateDirection), $text);
    }
}