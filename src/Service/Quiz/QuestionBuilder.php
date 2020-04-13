<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Service\Telegram\TranslationService;

final class QuestionBuilder
{
    private TranslationService $translation;

    public function __construct(TranslationService $translation)
    {
        $this->translation = $translation;
    }

    public function build(QuizStep $step): string
    {
        return $this->translation->translate(
            'messages.quiz_question',
            $step->getQuiz()->getChat()->getId(),
            [
                'text'          => $step->getCorrectWord()->getText(),
                'transcription' => $step->getCorrectWord()->getTranscription(),
            ]
        );
    }
}
