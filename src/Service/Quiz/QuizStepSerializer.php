<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

use function shuffle;

class QuizStepSerializer
{
    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function serialize(QuizStep $quizStep): InlineKeyboard
    {
        /** @var Word[] $words */
        $words = $quizStep->getWords()->toArray();
        shuffle($words);

        $line = [];
        foreach ($words as $word) {
            $line = count($line) % 2 === 0 ? [] : $line;
            $arr = $word->getTranslations()->toArray();
            /** @var Word $translation */
            $translation = reset($arr);

            $line[] = new InlineButton($translation->getText(), '/quiz_answer ' . $translation->getText());

            if (count($line) === 2) {
                $this->builder->addLine($line);
            }
        }

        return $this->builder->flush();
    }
}
