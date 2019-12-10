<?php

namespace Ig0rbm\Memo\Service\WordList;

use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\WordList;
use Ig0rbm\Memo\Repository\Telegram\Message\ChatRepository;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;

class WordListPreparer
{
    private WordListRepository $wordListRepository;

    private ChatRepository $chatRepository;

    public function __construct(WordListRepository $wordListRepository, ChatRepository $chatRepository)
    {
        $this->wordListRepository = $wordListRepository;
        $this->chatRepository     = $chatRepository;
    }

    public function prepare(Chat $chat): WordList
    {
        $wordList = $this->wordListRepository->findByChat($chat);
        if ($wordList) {
            return $wordList;
        }

        $wordList = new WordList();
        $wordList->setChat($chat);

        return $wordList;
    }
}
