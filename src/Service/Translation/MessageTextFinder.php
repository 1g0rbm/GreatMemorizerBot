<?php

namespace Ig0rbm\Memo\Service\Translation;

use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;

class MessageTextFinder
{
    /** @var TranslationTextParser */
    private $translationTextParser;

    public function __construct(TranslationTextParser $translationTextParser)
    {
        $this->translationTextParser = $translationTextParser;
    }

    public function find(MessageFrom $messageFrom): ?string
    {
        if ($messageFrom->getReply() === null) {
            return $this->translationTextParser->parse($messageFrom->getText()->getText());
        }

        return $this->translationTextParser->parse($messageFrom->getReply()->getText()->getText());
    }
}
