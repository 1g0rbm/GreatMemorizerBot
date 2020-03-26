<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\DBALException;
use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\QuizStepBuilderException;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Repository\Translation\WordRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

use function array_merge;
use function json_encode;
use function sprintf;

class QuizStepBuilderByWordList
{
    private WordRepository $wordRepository;

    private WordListRepository $wordListRepository;

    private AdapterInterface $cache;

    public function __construct(
        WordRepository $wordRepository,
        WordListRepository $wordListRepository,
        AdapterInterface $cache
    ) {
        $this->wordRepository     = $wordRepository;
        $this->wordListRepository = $wordListRepository;
        $this->cache              = $cache;
    }

    /**
     * @return Collection|QuizStep[]
     *
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function do(Quiz $quiz): Collection
    {
        if ($quiz->getWordListId() === null) {
            throw QuizStepBuilderException::becauseThereIsNoWordListId($quiz->getId());
        }

        $countWords = $this->wordListRepository->countAllWordsForChatAndPos(
            $quiz->getChat(),
            ['noun', 'verb', 'adjective', 'adverb']
        );
        $item       = $this->cache->getItem(sprintf('%d_exclude_ids', $quiz->getChat()->getId()));
        $excludeIds = $item->isHit() ? json_decode($item->get(), true) : [];
        $excludeIds = count($excludeIds) < $countWords - $quiz->getLength() ? $excludeIds : [];

        $rightWords = $this->wordRepository->getRandomWordsByWordListId(
            Direction::LANG_EN,
            ['noun', 'verb', 'adjective', 'adverb'],
            $quiz->getWordListId(),
            $quiz->getLength(),
            $excludeIds
        );

        $rightWordsIds = $rightWords->map(static fn(Word $word) => $word->getId())->toArray();
        $excludeIds    = array_merge($excludeIds, $rightWordsIds);

        $item->set(json_encode($excludeIds));
        $item->expiresAt(new DateTimeImmutable('+ 1 day'));

        $this->cache->save($item);

        $wrongWords = $this->wordRepository->getRandomWords(
            Direction::LANG_EN,
            ['noun', 'verb', 'adjective', 'adverb'],
            ($quiz->getLength() * QuizStep::DEFAULT_LENGTH) - QuizStep::DEFAULT_LENGTH,
            $rightWordsIds
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
