<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;

use function mb_strcut;
use function shuffle;
use function sprintf;

class QuizStepSerializer
{
    private const WORD_TEXT_ERR = '#ERR_NULL_DATA';

    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function serialize(QuizStep $quizStep): InlineKeyboard
    {
        /** @var Word[] $words */
        $words       = $quizStep->getWords()->toArray();
        $correctWord = $quizStep->getCorrectWord();
        shuffle($words);

        $line = [];
        foreach ($words as $word) {
            $line = count($line) % 2 === 0 ? [] : $line;

            if ($word->getId() === $correctWord->getId()) {
                /** @var Word $translation */
                $translation = $word->getTranslations()->first();
            } else {
                $arr = $word->getTranslations()->toArray();
                /** @var Word $translation */
                $translation = reset($arr);
            }

            $btnText = $translation->getText() ?? self::WORD_TEXT_ERR;
            $line[]  = new InlineButton(
                $btnText,
                mb_strcut(sprintf('/quiz_answer %s', $btnText), 0, 64)
            );

            if (count($line) === 2) {
                $this->builder->addLine($line);
            }
        }

        return $this->builder->flush();
    }
}
