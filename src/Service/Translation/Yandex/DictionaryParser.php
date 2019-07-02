<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Translation\Yandex\DictionaryParseException;

class DictionaryParser
{
    public function parse(string $json, Direction $direction): Word
    {
        $arr = json_decode($json, true);

        if (false === isset($arr['def'])) {
            DictionaryParseException::becauseFieldNotFound('def');
        }

        $word = new Word();
        $word->setLangCode($direction->getLangFrom());

        foreach ($arr['def'] as $item) {
            $this->build($item, $word, $direction);
        }

        return $word;
    }

    public function createWordsCollection(array $rawWord, Direction $direction): ArrayCollection
    {
        $collection = new ArrayCollection();
        $word = new Word();
        $word->setLangCode($direction->getLangTo());

        foreach ($rawWord as $item) {
            $this->build($item, $word, $direction);
            $collection->add($word);
        }

        return $collection;
    }

    private function build(array $item, Word &$word, Direction $direction): void
    {
        if (false === isset($item['text'])) {
            DictionaryParseException::becauseFieldNotFound('text');
        }

        if (false === isset($item['pos'])) {
            DictionaryParseException::becauseFieldNotFound('pos');
        }

        $word->setText($item['text']);
        $word->setPos($item['pos']);

        if (isset($item['ts'])) {
            $word->setTranscription($item['ts']);
        }

        if (isset($item['tr'])) {
            $word->setTranslations($this->createWordsCollection($item['tr'], $direction));
        }

        if (isset($item['syn'])) {
            $word->setSynonyms($this->createWordsCollection($item['syn'], $direction));
        }
    }
}