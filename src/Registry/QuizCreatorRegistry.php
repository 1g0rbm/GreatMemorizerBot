<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Registry;

use Ig0rbm\Memo\Exception\Registry\Quiz\RegistryCreatorNotFoundException;
use Ig0rbm\Memo\Exception\Registry\Quiz\RegistryDuplicateCreatorException;
use Ig0rbm\Memo\Service\Quiz\Creator\QuizCreatorInterface;

class QuizCreatorRegistry
{
    private array $container = [];

    public function getQuizCreator(string $type): QuizCreatorInterface
    {
        if (!isset($this->container[$type])) {
            RegistryCreatorNotFoundException::byType($type);
        }

        return $this->container[$type];
    }

    public function addCreator(QuizCreatorInterface $creator): void
    {
        if (isset($this->container[$creator->type()])) {
            throw RegistryDuplicateCreatorException::byType($creator->type());
        }

        $this->container[$creator->type()] = $creator;
    }
}
