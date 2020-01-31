<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\Quiz;
use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Exception\Quiz\ResultantException;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\Telegram\TranslationService;

use function implode;

class ResultantService
{
    private MessageBuilder $builder;

    private TranslationService $translation;

    public function __construct(MessageBuilder $builder, TranslationService $translation)
    {
        $this->builder     = $builder;
        $this->translation = $translation;
    }

    public function create(Quiz $quiz): string
    {
        if (!$quiz->isComplete()) {
            ResultantException::becauseQuizIsNotComplete($quiz->getId());
        }

        $builder    = $this->builder;
        $translator = $this->translation;
        $chatId     = $quiz->getChat()->getId();

        $result = $quiz->getSteps()->map(static function (QuizStep $step) use ($builder, $translator, $chatId) {
            /** @var Word $translation */
            $translation = $step->getCorrectWord()->getTranslations()->first();
            $builder->appendLn($step->isCorrect() ? '✅' : '❌')
                ->append($translator->translate('label.question_word', $chatId), MessageBuilder::BOLD)
                ->appendLn($step->getCorrectWord()->getText())
                ->append(
                    $translator->translate('label.correct_translation', $chatId),
                    MessageBuilder::BOLD
                )
                ->appendLn($translation->getText());

            return $builder->flush();
        });

        return implode(PHP_EOL, $result->toArray());
    }
}
