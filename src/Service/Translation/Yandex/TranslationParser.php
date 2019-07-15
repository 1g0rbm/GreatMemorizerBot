<?php

namespace Ig0rbm\Memo\Service\Translation\Yandex;

use Ig0rbm\Memo\Entity\Translation\Direction;
use Ig0rbm\Memo\Entity\Translation\Text;
use Ig0rbm\Memo\Exception\Translation\Yandex\TranslationParseException;

class TranslationParser
{
    /**
     * @param string $json
     * @param Direction $direction
     *
     * @return Text
     *
     * @throws TranslationParseException
     */
    public function parse(string $json, Direction $direction): Text
    {
        $translation = json_decode($json, true);
        $text = new Text();

        if (!isset($translation['text'])) {
            throw TranslationParseException::becauseFieldNotFound('text');
        }

        $text->setText($translation['text'][0]);
        $text->setLangCode($direction->getLangTo());

        return $text;
    }
}