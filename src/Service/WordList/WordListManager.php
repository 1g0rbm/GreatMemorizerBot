<?php

namespace Ig0rbm\Memo\Service\WordList;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Event\WordList\Telegraph\WordListEvent;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;

class WordListManager
{
    private EntityManagerInterface $em;

    private WordListPreparer $wordListPreparer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface $em,
        WordListPreparer $wordListPreparer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->wordListPreparer = $wordListPreparer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function add(Chat $chat, Collection $bag): void
    {
        $wordList = $this->wordListPreparer->prepare($chat);
        $bag->map(static fn (Word $word) => $wordList->addWord($word));

        if (false === $this->em->getUnitOfWork()->isInIdentityMap($wordList)) {
            $this->em->persist($wordList);
        }

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw WordListException::becauseListAlreadyHasSameWord();
        }

        $this->eventDispatcher->dispatch(WordListEvent::NAME, new WordListEvent($wordList));
    }
}
