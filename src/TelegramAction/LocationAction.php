<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use GuzzleHttp\Exception\GuzzleException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Exception\Telegram\LocationException;
use Ig0rbm\Memo\Service\Telegram\MessageBuilder;
use Ig0rbm\Memo\Service\TimeZone\AccountTimeZoneManager;

class LocationAction extends AbstractTelegramAction
{
    private AccountTimeZoneManager $timeZoneManager;

    private MessageBuilder $builder;

    public function __construct(AccountTimeZoneManager $timeZoneManager, MessageBuilder $builder)
    {
        $this->timeZoneManager = $timeZoneManager;
        $this->builder         = $builder;
    }

    /**
     * @throws GuzzleException
     */
    public function run(MessageFrom $messageFrom, Command $command): MessageTo
    {
        $to = new MessageTo();
        $to->setChatId($messageFrom->getChat()->getId());

        $location = $messageFrom->getLocation();
        if ($location === null) {
            LocationException::becauseThereIsNoLocationInMessage();
        }

        $account = $this->timeZoneManager->setTimeZoneForChat(
            $messageFrom->getChat(),
            $location->getLatitude(),
            $location->getLongitude()
        );

        $timezoneMessage = $this->translator->translate(
            'messages.timezone_thanks',
            $to->getChatId(),
            ['timezone' => $account->getTimeZone()]
        );

        $this->builder->appendLn($timezoneMessage)
            ->appendLn('')
            ->append($this->translator->translate('messages.quiz_creating_instruction', $to->getChatId()));

        $to->setText($this->builder->flush());

        return $to;
    }
}
