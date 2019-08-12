<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;

class TextTranslationService
{
    /** @var ApiTextTranslationInterface */
    private $apiTranslation;

    public function __construct(ApiTextTranslationInterface $apiTranslation)
    {
        $this->apiTranslation = $apiTranslation;
    }

    public function translate(Direction $direction, string $text): Text
    {
        return $this->apiTranslation->getTranslate($direction, $text);
    }
}