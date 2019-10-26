<?php

namespace Ig0rbm\Memo\Service\Quiz;

use Ig0rbm\Memo\Entity\Quiz\QuizStep;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineButton;
use Ig0rbm\Memo\Entity\Telegram\Message\InlineKeyboard;
use Ig0rbm\Memo\Entity\Translation\Word;
use Ig0rbm\Memo\Service\Telegram\InlineKeyboard\Builder;
use Psr\Log\LoggerInterface;

class QuizStepSerializer
{
    /** @var Builder */
    private $builder;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Builder $builder, LoggerInterface $logger)
    {
        $this->builder = $builder;
        $this->logger = $logger;
    }

    public function serialize(QuizStep $quizStep): InlineKeyboard
    {
        $line = [];
        foreach ($quizStep->getWords() as $word) {
            $line = count($line) % 2 === 0 ? [] : $line;
            $arr = $word->getTranslations()->toArray();
            /** @var Word $translation */
            $translation = reset($arr);

            $line[] = new InlineButton($translation->getText(), '/quiz');

            if (count($line) === 2) {
                $this->builder->addLine($line);
            }
        }

        return $this->builder->flush();
    }
}
