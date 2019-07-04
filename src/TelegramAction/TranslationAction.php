<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\Action\ActionInterface;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\Yandex\YandexDictionaryApiService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var YandexDictionaryApiService */
    private $dictionaryApi;

    /** @var DirectionParser */
    private $directionParser;

    public function __construct(
        YandexDictionaryApiService $dictionaryApi,
        DirectionParser $directionParser
    ) {
        $this->dictionaryApi = $dictionaryApi;
        $this->directionParser = $directionParser;
    }

    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $messageTo = new MessageTo();
        $messageTo->setChatId($messageFrom->getChat()->getId());

        if (null === $messageFrom->getText()->getText()) {
            $messageTo->setText('Wrong text');
            return $messageTo;
        }

        $response = $this->dictionaryApi->getTranslate(
            $this->directionParser->parse('en-ru'),
            $messageFrom->getText()->getText()
        );

        $messageTo->setText($response->getText() ?? 'No word');

        return $messageTo;
    }
}