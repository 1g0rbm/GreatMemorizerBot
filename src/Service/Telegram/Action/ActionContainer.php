<?php

namespace Ig0rbm\Memo\Service\Telegram\Action;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ActionContainer
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $actionName): ActionInterface
    {
        /** @var ActionInterface $actionName */
        $actionName = $this->container->get($actionName);

        return $actionName;
    }
}
