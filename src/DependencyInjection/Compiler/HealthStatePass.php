<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\HealthStateRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HealthStatePass implements CompilerPassInterface
{
    public const HEALTH_STATE_TAG = 'dynamic_search.health_state';

    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(HealthStateRegistry::class);
        foreach ($this->findAndSortTaggedServices(self::HEALTH_STATE_TAG, $container) as $reference) {
            $definition->addMethodCall('register', [$reference]);
        }
    }
}
