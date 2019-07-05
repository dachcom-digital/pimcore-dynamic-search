<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\ResourceNormalizerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ResourceNormalizerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('dynamic_search.resource_normalizer', true) as $id => $tags) {
            $definition = $container->getDefinition(ResourceNormalizerRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerNormalizer', [new Reference($id), $attributes['identifier'], $attributes['data_provider']]);
            }
        }

        foreach ($container->findTaggedServiceIds('dynamic_search.resource_id_builder', true) as $id => $tags) {
            $definition = $container->getDefinition(ResourceNormalizerRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerIdBuilder', [new Reference($id), $attributes['identifier'], $attributes['data_provider']]);
            }
        }
    }
}