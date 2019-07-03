<?php

namespace Ig0rbm\Memo\TelegramAction;

use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\Telegram\TelegramApiService;
use Ig0rbm\Memo\Service\Translation\DirectionParser;
use Ig0rbm\Memo\Service\Translation\Yandex\YandexDictionaryApiService;

class TranslationAction extends AbstractTelegramAction
{
    /** @var YandexDictionaryApiService */
    private $dictionaryApi;

    /** @var DirectionParser */
    private $directionParser;

    public function __construct(
        TelegramApiService $api,
        YandexDictionaryApiService $dictionaryApi,
        DirectionParser $directionParser
    )
    {
        parent::__construct($api);

        $this->dictionaryApi = $dictionaryApi;
        $this->directionParser = $directionParser;
    }

    public function run(MessageFrom $messageFrom, Command $command): void
    {
        $response = $this->dictionaryApi->getTranslate(
            $this->directionParser->parse('en-ru'),
            $messageFrom->getText()->getText()
        );

        $messageTo = new MessageTo();
        $messageTo->setText($response->getText());
        $messageTo->setChatId($messageFrom->getChat()->getId());

        $this->api->sendMessage($messageTo);
    }
}