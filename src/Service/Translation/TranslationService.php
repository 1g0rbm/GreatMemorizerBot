<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Service\Telegram\MessageBuilder;

class TranslationService
{
    /** @var ApiWordTranslationInterface */
    private $apiWordTranslation;

    /** @var DirectionParser */
    private $directionParser;

    /** @var ApiTextTranslationInterface */
    private $apiTextTranslation;

    /** @var MessageBuilder */
    private $messageBuilder;

    public function __construct(
        ApiWordTranslationInterface $apiWordTranslation,
        ApiTextTranslationInterface $apiTextTranslation,
        DirectionParser $directionParser,
        MessageBuilder $messageBuilder
    ) {
        $this->apiWordTranslation = $apiWordTranslation;
        $this->apiTextTranslation = $apiTextTranslation;
        $this->directionParser = $directionParser;
        $this->messageBuilder = $messageBuilder;
    }

    public function translate(string $translateDirection, string $string): string
    {
        $direction = $this->directionParser->parse($translateDirection);

        $words = $this->apiWordTranslation->getTranslate($direction, $string);
        if ($words->count() > 0) {
            return $this->messageBuilder->buildFromWords($words);
        }

        return $this->messageBuilder->buildFromText($this->apiTextTranslation->getTranslate($direction, $string));
    }
}