<?php

namespace Ig0rbm\Memo\EventListener;

use Ig0rbm\Memo\Event\WordList\Telegraph\WordListEvent;
use Ig0rbm\Memo\Service\WordList\Telegraph\WordListManager;
use Psr\Log\LoggerInterface;

class WordListEventListener
{
    /** @var WordListManager */
    private $manager;

    public function __construct(WordListManager $manager)
    {
        $this->manager = $manager;
    }

    public function onWordListAction(WordListEvent $event): void
    {
        $this->manager->sendPage($event->getWordList());
    }
}
