<?php

namespace Ig0rbm\Memo\Service\Telegram;

use Exception;
use Traversable;
use Iterator;
use Doctrine\Common\Collections\Collection;
use Ig0rbm\Memo\Entity\Translation\Text;
use Ig0rbm\Memo\Entity\Translation\Word;

class TranslationMessageBuilder
{
    private string $string = '';

    /**
     * @throws Exception
     */
    public function buildFromCollection(Collection $collection): string
    {
        return $this->buildFromIterable($collection->getIterator());
    }

    /**
     * @throws Exception
     */
    public function buildFromWords(Collection $words): string
    {
        return $this->buildFromIterable($words->getIterator());
    }

    /**
     * @throws Exception
     */
    public function buildFromIterable(Traversable $wordsIterator): string
    {
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
        $this->append($text->getText())->appendBreak();

        return $this->string;
    }

    /**
     * @param Collection|Word[] $collection
     * @return $this
     * @throws Exception
     */
    private function appendSynonyms(Collection $collection): self
    {
        /** @var Iterator $iterator */
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

    /**
     * @param Collection $translation
     *
     * @return $this
     *
     * @throws Exception
     */
    private function appendTranslation(Collection $translation): self
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