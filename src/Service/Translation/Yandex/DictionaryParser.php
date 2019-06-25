<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Translation\Yandex\DictionaryParseException;

class DictionaryParser
{
    public function parse(string $json): Word
    {
        $arr = json_decode($json, true);

        if (false === isset($arr['def'])) {
            DictionaryParseException::becauseFieldNotFound('def');
        }

        $def = $arr['def'];
        $word = new Word();

        foreach ($def as $item) {
            if (false === $item['text']) {
                DictionaryParseException::becauseFieldNotFound('text');
            }

            $word->setText($item['text']);
        }

        return $word;
    }
}