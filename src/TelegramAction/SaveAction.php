<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\TranslationService;
use Ig0rbm\Memo\Service\Translation\WordTranslationService;

class SaveAction extends AbstractTelegramAction
{
    /** @var TranslationService */
    private $translation;

    /** @var WordTranslationService */
    private $wordTranslation;

    /** @var DirectionParser */
    private $directionParser;

    public function __construct(
        TranslationService $translation,
        WordTranslationService $wordTranslation,
        DirectionParser $directionParser
    ) {
        $this->translation = $translation;
        $this->wordTranslation = $wordTranslation;
        $this->directionParser = $directionParser;
    }

    /**
     * @throws ORMException
     */
    public function run(MessageFrom $from, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($from->getChat()->getId());

        $wordsBag = $this->wordTranslation->translate(
            $this->directionParser->parse('en-ru'),
            $from->getText()->getText()
        );

        if ($wordsBag->count() === 0) {
            $messageTo->setText('Wrong word for save');

            return $messageTo;
        }

//        $this->wordListPersistService->persist($from->getChat(), $wordsBag);

        $messageTo->setText('Word was saved successfully');

        return $messageTo;
    }
}