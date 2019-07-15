<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Doctrine\Common\Collections\ArrayCollection;
use Ig0rbm\HandyBag\HandyBag;
use Ig0rbm\Memo\Entity\Translation\Text;
use Ig0rbm\Memo\Entity\Translation\Word;

class MessageBuilder
{
    /** @var string */
    private $string = '';

    public function buildFromWords(HandyBag $words): string
    {
        $wordsIterator = $words->getIterator();
        while ($wordsIterator->valid()) {
            /** @var Word $word */
            $word = $wordsIterator->current();
            $this->appendAsBold(sprintf('%s: ', $word->getText()))
                ->appendAsBold(sprintf('%s ', $word->getPos()))
                ->appendAsBold(sprintf('[%s]', $word->getTranscription()))
                ->appendBreak()
                ->appendTranslation($word->getTranslations());

            $wordsIterator->next();

            if ($wordsIterator->valid()) {
                $this->appendBreak();
            }
        }

        return $this->string;
    }

    public function buildFromText(Text $text): string
    {
        $this->appendAsBold('Translation: ')
            ->appendBreak()
            ->append($text->getText())
            ->appendBreak();

        return $this->string;
    }

    private function appendSynonyms(ArrayCollection $collection): self
    {
        $iterator = $collection->getIterator();
        while ($iterator->valid()) {
            /** @var Word $translation */
            $translation = $iterator->current();
            $this->append(' | ')
                ->append($translation->getText());

            $iterator->next();
        }

        return $this;
    }

    private function appendTranslation(ArrayCollection $translation): self
    {
        $translationIterator = $translation->getIterator();
        while ($translationIterator->valid()) {
            /** @var Word $translation */
            $translation = $translationIterator->current();
            $this->append('    ')
                ->append($translation->getText());

            if ($translation->getSynonyms()) {
                $this->appendSynonyms($translation->getSynonyms());
            }

            $this->appendBreak();

            $translationIterator->next();
        }

        return $this;
    }

    private function appendBreak(): self
    {
        return $this->append("\n");
    }

    private function appendAsBold(string $string): self
    {
        return $this->append(sprintf('*%s*', $string));
    }

    private function append(string $string): self
    {
        $this->string = $this->string . $string;

        return $this;
    }
}