<?php

namespace Ig0rbm\Memo\Service\Translation;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Repository\Translation\WordRepository;

class WordTranslationService
{
    private ApiWordTranslationInterface $wordTranslation;

    private WordRepository $wordRepository;

    private WordsPersistService $wordsPersistService;

    public function __construct(
        ApiWordTranslationInterface $wordTranslation,
        WordRepository $wordRepository,
        WordsPersistService $wordsPersistService
    ) {
        $this->wordTranslation     = $wordTranslation;
        $this->wordRepository      = $wordRepository;
        $this->wordsPersistService = $wordsPersistService;
    }

    /**
     * @throws ORMException
     */
    public function translate(Direction $direction, string $string): Collection
    {
        $words = $direction->isSavable() ? $this->wordRepository->findWordsCollection($string) : null;
        if ($words) {
            return $words;
        }

        $words = $this->wordTranslation->getTranslate($direction, $string);

        if ($direction->isSavable()) {
            $this->wordsPersistService->save($words);
        }

        return $words;
    }
}
