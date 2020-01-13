<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\TelegramAction;

use GuzzleHttp\Exception\GuzzleException;
use Ig0rbm\Memo\Entity\Telegram\Command\Command;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageFrom;
use Ig0rbm\Memo\Entity\Telegram\Message\MessageTo;
use Ig0rbm\Memo\Service\TimeZone\AccountTimeZoneManager;
use Ig0rbm\Memo\Exception\Telegram\LocationException;

class LocationAction extends AbstractTelegramAction
{
    private AccountTimeZoneManager $timeZoneManager;

    public function __construct(AccountTimeZoneManager $timeZoneManager)
    {
        $this->timeZoneManager = $timeZoneManager;
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

        $to->setText(sprintf('Now your TimeZone is %s', $account->getTimeZone()));

        return $to;
    }
}
