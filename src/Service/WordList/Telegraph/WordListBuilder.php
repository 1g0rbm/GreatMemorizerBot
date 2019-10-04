<?php

namespace Ig0rbm\Memo\Service\WordList\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Content\H4Node;
use Ig0rbm\Memo\Entity\Telegraph\Content\ItalicNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ParagraphNode;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;

class WordListBuilder
{
    public function build(WordList $wordList): ParagraphNode
    {
        $words = $wordList->getWords()->toArray();
        $list = new ParagraphNode();

        /** @var Word $word */
        foreach ($words as $word) {
            $head = new H4Node();
            $head->setText(
                sprintf(
                    '%s: %s [%s]',
                    $word->getText(),
                    $word->getPos(),
                    $word->getTranscription() ?? 'NULL'
                )
            );

            $list->addChild($head);

            if ($word->getTranslations()) {
                /** @var Word $translation */
                foreach ($word->getTranslations()->toArray() as $translation) {
                    $line = $translation->getText();

                    if ($translation->getSynonyms()) {
                        /** @var Word $synonym */
                        foreach ($translation->getSynonyms() as $synonym) {
                            $line .= ', ' . $synonym->getText();
                        }
                    }

                    $i = new ItalicNode();
                    $i->setText($line);

                    $p = new ParagraphNode();
                    $p->addChild($i);

                    $list->addChild($p);
                }
            }
        }

        return $list;
    }
}
