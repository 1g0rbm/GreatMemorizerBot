<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;

class TranslationService
{
    /** @var WordTranslationService */
    private $wordTranslation;

    /** @var TextTranslationService */
    private $textTranslation;

    /** @var WordRepository */
    private $wordRepository;

    /** @var DirectionParser */
    private $directionParser;

    /** @var MessageBuilder */
    private $messageBuilder;

    public function __construct(
        WordTranslationService $wordTranslation,
        TextTranslationService $textTranslation,
        WordRepository $wordRepository,
        DirectionParser $directionParser,
        MessageBuilder $messageBuilder
    ) {
        $this->wordTranslation = $wordTranslation;
        $this->textTranslation = $textTranslation;
        $this->wordRepository = $wordRepository;
        $this->directionParser = $directionParser;
        $this->messageBuilder = $messageBuilder;
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
