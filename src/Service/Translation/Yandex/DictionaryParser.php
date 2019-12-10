<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\Memo\Collection\Translation\WordsBag;
use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Translation\Yandex\DictionaryParseException;

class DictionaryParser
{
    public function parse(string $json, Direction $direction): ?WordsBag
    {
        $translations = new WordsBag();
        $arr = json_decode($json, true);

        if (false === isset($arr['def'])) {
            DictionaryParseException::becauseFieldNotFound('def');
        }

        foreach ($arr['def'] as $item) {
            $word = new Word();
            $word->setLangCode($direction->getLangFrom());
            $word = $this->build($item, $word, $direction);

            $translations->set($word->getPos(), $word);
        }

        return $translations;
    }

    /**
     * @param mixed[] $rawWord
     * @param Direction $direction
     *
     * @return ArrayCollection
     *
     * @throws DictionaryParseException
     */
    public function createWordsCollection(array $rawWord, Direction $direction): ArrayCollection
    {
        $collection = new ArrayCollection();

        foreach ($rawWord as $item) {

            $word = new Word();
            $word->setLangCode($direction->getLangTo());

            $collection->add($this->build($item, $word, $direction));
        }

        return $collection;
    }

    /**
     * @param mixed[] $item
     * @param Word $word
     * @param Direction $direction
     *
     * @return Word
     *
     * @throws DictionaryParseException
     */
    private function build(array $item, Word $word, Direction $direction): Word
    {
        if (false === isset($item['text'])) {
            throw DictionaryParseException::becauseFieldNotFound('text');
        }

        $word->setText($item['text']);
        $word->setPos($item['pos'] ?? 'unclear');

        if (isset($item['ts'])) {
            $word->setTranscription($item['ts']);
        }

        if (isset($item['tr'])) {
            $word->setTranslations($this->createWordsCollection($item['tr'], $direction));
        }

        if (isset($item['syn'])) {
            $word->setSynonyms($this->createWordsCollection($item['syn'], $direction));
        }

        return $word;
    }
}