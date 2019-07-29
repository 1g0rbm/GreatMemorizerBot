<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
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

    /** @var WordRepository */
    private $wordRepository;

    /** @var EntityFlusher */
    private $flusher;

    public function __construct(
        ApiWordTranslationInterface $apiWordTranslation,
        ApiTextTranslationInterface $apiTextTranslation,
        DirectionParser $directionParser,
        MessageBuilder $messageBuilder,
        WordRepository $wordRepository,
        EntityFlusher $flusher
    ) {
        $this->apiWordTranslation = $apiWordTranslation;
        $this->apiTextTranslation = $apiTextTranslation;
        $this->directionParser = $directionParser;
        $this->messageBuilder = $messageBuilder;
        $this->wordRepository = $wordRepository;
        $this->flusher = $flusher;
    }

    /**
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function translate(string $translateDirection, string $string): string
    {
        $direction = $this->directionParser->parse($translateDirection);

        $words = $this->wordRepository->findWordsCollection($string);
        if ($words) {
            return $this->messageBuilder->buildFromWords($words);
        }

        $words = $this->apiWordTranslation->getTranslate($direction, $string);
        if ($words->count() > 0) {
            $this->saveWordsCollection($words);

            return $this->messageBuilder->buildFromWords($words);
        }

        return $this->messageBuilder->buildFromText($this->apiTextTranslation->getTranslate($direction, $string));
    }

    /**
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    private function saveWordsCollection(HandyBag $words): void
    {
        /** @var Word $word */
        foreach ($words->getIterator() as $word) {
            if ($this->wordRepository->findOneByText($word->getText())) {
                return;
            }

            $this->wordRepository->addWord($word);
        }

        $this->flusher->flush();
    }
}
