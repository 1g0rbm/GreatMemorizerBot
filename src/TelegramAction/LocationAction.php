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

        $this->builder->appendLn(sprintf('Thanks! Now your TimeZone is "%s"', $account->getTimeZone()))
            ->appendLn('')
            ->append('For creating regular quiz you must write message by pattern ')
            ->append('HH:MM', MessageBuilder::BOLD)
            ->appendLn('.')
            ->append('For example ')
            ->appendLn('12:45', MessageBuilder::BOLD);

        $to->setText($this->builder->flush());

        return $to;
    }
}
