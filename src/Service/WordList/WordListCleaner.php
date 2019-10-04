<?php

namespace Ig0rbm\Memo\Service\WordList;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ig0rbm\Memo\Event\WordList\Telegraph\WordListEvent;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\WordList\WordListCleanerException;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;

class WordListCleaner
{
    /** @var WordListRepository */
    private $repository;

    /** @var EntityFlusher */
    private $flusher;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        WordListRepository $repository,
        EntityFlusher $flusher,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repository = $repository;
        $this->flusher = $flusher;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function clean(Chat $chat, string $cleanWord): WordList
    {
        $wordList = $this->repository->findByChat($chat);
        if ($wordList === null) {
            throw WordListCleanerException::becauseThereIsNoWordListForChat($chat->getId());
        }

        $collection = $wordList->getWords()->filter(function (Word $word) use ($cleanWord) {
            return $word->getText() !== $cleanWord;
        });

        $wordList->setWords($collection);
        $this->flusher->flush();

        $this->eventDispatcher->dispatch(WordListEvent::NAME, new WordListEvent($wordList));

        return $wordList;
    }
}
