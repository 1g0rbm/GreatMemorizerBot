<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Exception\WordList;

use DomainException;

class WordListException extends DomainException
{
    public static function becauseThereIsNotListForId(int $listId): self
    {
        return new self(sprintf('There isn\'t list for id %d', $listId));
    }

    public static function becauseListAlreadyHasSameWord(): self
    {
        return new self('messages.save.word_exist');
    }

    public static function becauseThereIsNotListForChat(int $chatId): self
    {
        return new self(sprintf('There isn\'t account for chat_id %d', $chatId));
    }
}
