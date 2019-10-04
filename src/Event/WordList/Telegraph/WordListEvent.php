<?php

namespace Ig0rbm\Memo\Event\WordList\Telegraph;

use Symfony\Component\EventDispatcher\Event;
use Ig0rbm\Memo\Entity\Translation\WordList;

class WordListEvent extends Event
{
    public const NAME = 'memo.word_list_event';

    /** @var WordList */
    private $wordList;

    public function __construct(WordList $wordList)
    {
        $this->wordList = $wordList;
    }

    public function getWordList(): WordList
    {
        return $this->wordList;
    }
}
