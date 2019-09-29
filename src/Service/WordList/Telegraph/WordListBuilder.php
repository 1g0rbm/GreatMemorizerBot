<?php

namespace Ig0rbm\Memo\Service\WordList\Telegraph;

use Ig0rbm\Memo\Entity\Telegraph\Content\ListItemNode;
use Ig0rbm\Memo\Entity\Telegraph\Content\ListNode;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Entity\Translation\WordList;

class WordListBuilder
{
    public function build(WordList $wordList): ListNode
    {
        $ul    = new ListNode();
        $words = $wordList->getWords()->toArray();

        /** @var Word $word */
        foreach ($words as $word) {
            $li = new ListItemNode();
            $li->setText(sprintf('%s [%s]', $word->getText(), $word->getTranscription() ?? 'NULL'));
            $ul->addChild($li);
        }

        return $ul;
    }
}
