<?php

namespace Ig0rbm\Memo\TelegramAction;

use Doctrine\ORM\ORMException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\WordList\WordListException;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\MessageTextFinder;
use Ig0rbm\Memo\Service\Translation\WordTranslationService;
use Ig0rbm\Memo\Service\WordList\WordListManager;

class SaveAction extends AbstractTelegramAction
{
    /** @var WordTranslationService */
    private $wordTranslation;

    /** @var DirectionParser */
    private $directionParser;

    /** @var WordListManager */
    private $manager;

    /** @var MessageTextFinder */
    private $textFinder;

    public function __construct(
        WordTranslationService $wordTranslation,
        DirectionParser $directionParser,
        WordListManager $manager,
        MessageTextFinder $textFinder
    ) {
        $this->wordTranslation = $wordTranslation;
        $this->directionParser = $directionParser;
        $this->manager = $manager;
        $this->textFinder = $textFinder;
    }

    /**
     * @throws ORMException
     */
    public function run(MessageFrom $from, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($from->getChat()->getId());

        $text = $this->textFinder->find($from);

        $wordsBag = $this->wordTranslation->translate($this->directionParser->parse('en-ru'), $text);

        if ($wordsBag->count() === 0) {
            $messageTo->setText('Wrong word for save');

            return $messageTo;
        }

        try {
            $this->manager->add($from->getChat(), $wordsBag);
            $messageTo->setText('Word was saved successfully');
        } catch (WordListException $e) {
            $messageTo->setText($e->getMessage());
        }

        return $messageTo;
    }
}
