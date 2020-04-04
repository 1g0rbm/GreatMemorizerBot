<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\WordList;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Event\WordList\Telegraph\WordListEvent;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $bag->map(static function (Word $word) use ($wordList): void {
            if ($wordList->containsWord($word)) {
                throw WordListException::becauseListAlreadyHasSameWord();
            }

            $wordList->addWord($word);
        });

        if (false === $this->em->getUnitOfWork()->isInIdentityMap($wordList)) {
            $this->em->persist($wordList);
        }

        $this->em->flush();
        $this->eventDispatcher->dispatch(WordListEvent::NAME, new WordListEvent($wordList));
    }
}
