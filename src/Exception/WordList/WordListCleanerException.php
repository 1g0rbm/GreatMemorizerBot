<?php

namespace Ig0rbm\Memo\Exception\WordList;

use RuntimeException;

class WordListCleanerException extends RuntimeException
{
    public static function becauseThereIsNoWordListForChat(int $chatId): self
    {
        return new self(sprintf('No word list for chat with id = %d', $chatId));
    }
}
