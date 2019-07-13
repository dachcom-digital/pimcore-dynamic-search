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
                $definition->addMethodCall('registerResourceNormalizer', [new Reference($id), $attributes['identifier'], $attributes['data_provider']]);
            }
        }

        foreach ($container->findTaggedServiceIds('dynamic_search.document_normalizer', true) as $id => $tags) {
            $definition = $container->getDefinition(ResourceNormalizerRegistry::class);
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerDocumentNormalizer', [new Reference($id), $attributes['identifier'], $attributes['index_provider']]);
            }
        }
    }
}
