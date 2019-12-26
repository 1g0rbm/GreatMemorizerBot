<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventListener;

use Ig0rbm\Memo\Event\WordList\Telegraph\WordListEvent;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListManager;

class WordListEventListener
{
    private WordListManager $manager;

    public function __construct(WordListManager $manager)
    {
        $this->manager = $manager;
    }

    public function onWordListAction(WordListEvent $event): void
    {
        $this->manager->sendPage($event->getWordList());
    }
}
