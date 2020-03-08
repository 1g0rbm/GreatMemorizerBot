<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Doctrine\DBAL\DBALException;

class QuizStepBuilderByWordList
{
    private WordRepository $wordRepository;

    public function __construct(WordRepository $wordRepository)
    {
        $this->wordRepository = $wordRepository;
    }

    /**
     * @return Collection|QuizStep[]
     *
     * @throws DBALException
     */
    public function do(Quiz $quiz): Collection
    {
        if ($quiz->getWordListId() === null) {
            throw QuizStepBuilderException::becauseThereIsNoWordListId($quiz->getId());
        }

        $rightWords = $this->wordRepository->getRandomWordsByWordListId(
            Direction::LANG_EN,
            ['noun', 'verb', 'adjective', 'adverb'],
            $quiz->getWordListId(),
            $quiz->getLength()
        );
        $wrongWords = $this->wordRepository->getRandomWords(
            Direction::LANG_EN,
            ['noun', 'verb', 'adjective', 'adverb'],
            ($quiz->getLength() * QuizStep::DEFAULT_LENGTH) - QuizStep::DEFAULT_LENGTH,
            $rightWords->map(static fn(Word $word) => $word->getId())->toArray()
        )
        ->toArray();

        $collection = new ArrayCollection();
        $offset     = 0;
        $limit      = QuizStep::DEFAULT_LENGTH - 1;

        foreach ($rightWords as $rightWord) {
            $step = new QuizStep($quiz);

            $step->setCorrectWord($rightWord);
            $step->getWords()->add($rightWord);

            $stepWrongWords = array_slice($wrongWords, $offset, $limit);
            $offset        += $limit;

            foreach ($stepWrongWords as $wrongWord) {
                $step->getWords()->add($wrongWord);
            }

            $collection->add($step);
        }

        return $collection;
    }
}
