<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\IndexRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class IndexPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('dynamic_search.index.field', true) as $id => $tags) {
            $definition = $container->getDefinition(IndexRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerField', [new Reference($id), $attributes['identifier'], $attributes['index_provider']]);
            }
        }

        foreach ($container->findTaggedServiceIds('dynamic_search.index.filter', true) as $id => $tags) {
            $definition = $container->getDefinition(IndexRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerFilter', [new Reference($id), $attributes['identifier'], $attributes['index_provider']]);
            }
        }
    }
}
