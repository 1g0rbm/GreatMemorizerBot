<?php

namespace Ig0rbm\Memo\Collection\Translation;

use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Word;

class WordsBag extends HandyBag
{
    public function setWord(Word $word): void
    {
        $this->set($word->getPos(), $word);
    }

    public function getWordByPos(string $pos): ?Word
    {
        return  $this->get($pos);
    }
}