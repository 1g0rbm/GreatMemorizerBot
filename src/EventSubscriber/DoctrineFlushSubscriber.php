<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ig0rbm\Memo\Exception\Validator\EntityValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrineFlushSubscriber implements EventSubscriber
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Events::prePersist, Events::preUpdate];
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->validate($args->getEntity());
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->validate($args->getEntity());
    }

    public function validate(object $entity): void
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            throw new EntityValidationException((string) $errors);
        }
    }
}
