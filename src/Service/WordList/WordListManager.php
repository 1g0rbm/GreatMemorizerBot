<?php

namespace Ig0rbm\Memo\Service\WordList;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Exception\WordListException;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\EntityFlusher;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Psr\Log\LoggerInterface;

class WordListManager
{
    /** @var WordListRepository */
    private $wordListRepository;

    /** @var EntityFlusher */
    private $flusher;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        WordListRepository $wordListRepository,
        EntityFlusher $flusher,
        LoggerInterface $logger
    ) {
        $this->wordListRepository = $wordListRepository;
        $this->flusher = $flusher;
        $this->logger = $logger;
    }

    /**
     * @throws ORMException
     */
    public function add(Chat $chat, WordsBag $bag): void
    {
        $wordList = $this->wordListRepository->findByChat($chat);
        if ($wordList) {
            $bag->walk(
                static function ($key, Word $word) use ($wordList) {
                    $wordList->addWord($word);
                }
            );
        } else {
            $wordList = new WordList();
            $wordList->setChat($chat);

            $bag->walk(
                static function ($key, Word $word) use ($wordList) {
                    $wordList->addWord($word);
                }
            );

            $this->wordListRepository->addWordList($wordList);
        }

        try {
            $this->flusher->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw WordListException::becauseListAlreadyHasSameWord();
        }
    }
}
