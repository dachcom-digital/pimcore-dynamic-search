<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\ContextGuardRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContextGuardPass implements CompilerPassInterface
{
    public const CONTEXT_GUARD_TAG = 'dynamic_search.context_guard';

    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(ContextGuardRegistry::class);
        foreach ($this->findAndSortTaggedServices(self::CONTEXT_GUARD_TAG, $container) as $reference) {
            $definition->addMethodCall('register', [$reference]);
        }
    }
}
