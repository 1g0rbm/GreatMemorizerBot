<?php

namespace Ig0rbm\Memo\Service\WordList;

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

    public function __construct(WordListRepository $repository, EntityFlusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
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

        return $wordList;
    }
}
