<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Repository\Translation\WordRepository;

class WordTranslationService
{
    /** @var ApiWordTranslationInterface */
    private $wordTranslation;

    /** @var WordRepository */
    private $wordRepository;

    /** @var WordsPersistService */
    private $wordsPersistService;

    public function __construct(
        ApiWordTranslationInterface $wordTranslation,
        WordRepository $wordRepository,
        WordsPersistService $wordsPersistService
    ) {
        $this->wordTranslation = $wordTranslation;
        $this->wordRepository = $wordRepository;
        $this->wordsPersistService = $wordsPersistService;
    }

    /**
     * @throws ORMException
     */
    public function translate(Direction $direction, string $string): WordsBag
    {
        $words = $this->wordRepository->findWordsCollection($string);
        if ($words) {
            return $words;
        }

        $words = $this->wordTranslation->getTranslate($direction, $string);
        $this->wordsPersistService->save($words);

        return $words;
    }
}
