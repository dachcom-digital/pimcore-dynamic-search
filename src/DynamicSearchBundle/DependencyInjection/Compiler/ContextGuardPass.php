<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\ContextGuardRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContextGuardPass implements CompilerPassInterface
{
     use PriorityTaggedServiceTrait;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(ContextGuardRegistry::class);
        foreach ($this->findAndSortTaggedServices('dynamic_search.context_guard', $container) as $reference) {
            $definition->addMethodCall('register', [$reference]);
        }
    }
}
