<?php

namespace Ig0rbm\Memo\Service\WordList;

use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Telegram\Message\Chat;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Repository\Translation\WordListRepository;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Psr\Log\LoggerInterface;

class WordListShowService
{
    /** @var WordListRepository */
    private $wordListRepository;

    /** @var MessageBuilder */
    private $builder;

    public function __construct(WordListRepository $wordListRepository, MessageBuilder $builder)
    {
        $this->wordListRepository = $wordListRepository;
        $this->builder = $builder;
    }

    public function findByChat(Chat $chat): ?string
    {
        $list = $this->wordListRepository->findByChat($chat);
        if ($list === null) {
            return null;
        }

        return $this->builder->buildFromCollection($list->getWords()) ?: null;
    }
}
