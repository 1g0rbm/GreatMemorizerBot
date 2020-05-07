<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventListener\Telegram;

use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Watch\ClientActionLog;
use Ig0rbm\Memo\Event\Telegram\AfterParseRequestEvent;

class AfterParseRequestEventListener
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onAfterParseRequest(AfterParseRequestEvent $event): void
    {
        $form = $event->getFrom();

        $log = new ClientActionLog(
            $form->getChat(),
            $form->getCallbackCommand() ?? $form->getText()->getCommand(),
            $form->getCallbackQuery() ?
                $form->getCallbackQuery()->getData()->getText() :
                $form->getText()->getText()
        );

        $this->em->persist($log);
        $this->em->flush();
    }
}
