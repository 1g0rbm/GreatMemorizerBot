<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\DependencyInjection\Compiler;

use Ig0rbm\Memo\Registry\QuizCreatorRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QuizCreatorPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(QuizCreatorRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(QuizCreatorRegistry::class);
        $services   = $container->findTaggedServiceIds('service.quiz.create');

        foreach ($services as $id => $tags) {
            $definition->addMethodCall('addCreator', [new Reference($id)]);
        }
    }
}
