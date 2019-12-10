<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\Telegram\TranslationMessageBuilder;

class TranslationService
{
    private WordTranslationService $wordTranslation;

    private TextTranslationService $textTranslation;

    private WordRepository $wordRepository;

    private DirectionParser $directionParser;

    private TranslationMessageBuilder $messageBuilder;

    public function __construct(
        WordTranslationService $wordTranslation,
        TextTranslationService $textTranslation,
        WordRepository $wordRepository,
        DirectionParser $directionParser,
        TranslationMessageBuilder $messageBuilder
    ) {
        $this->wordTranslation = $wordTranslation;
        $this->textTranslation = $textTranslation;
        $this->wordRepository  = $wordRepository;
        $this->directionParser = $directionParser;
        $this->messageBuilder  = $messageBuilder;
    }

    /**
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function translate(Direction $direction, string $string): string
    {
        $words = $this->wordRepository->findWordsCollection($string);
        if ($words) {
            return $this->messageBuilder->buildFromWords($words);
        }

        $words = $this->wordTranslation->translate($direction, $string);
        if ($words->count() > 0) {
            return $this->messageBuilder->buildFromWords($words);
        }

        return $this->messageBuilder->buildFromText($this->textTranslation->translate($direction, $string));
    }
}
