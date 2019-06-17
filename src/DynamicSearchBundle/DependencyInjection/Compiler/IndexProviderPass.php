<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\IndexProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class IndexProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(IndexProviderRegistry::class);

        foreach ($container->findTaggedServiceIds('dynamic_search.index_provider', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('register', [new Reference($id), $attributes['alias']]);
            }
        }

        foreach ($container->findTaggedServiceIds('dynamic_search.index_provider.output_channel', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $validTypes = ['autocomplete', 'search'];
                if (!in_array($attributes['type'], $validTypes)) {
                    throw new \InvalidArgumentException(sprintf('"%s" is an invalid output channel type. Channel needs to be one of %s', $attributes['type'],
                        implode(', ', $validTypes)));

                }
                $definition->addMethodCall('registerOutputChannel', [new Reference($id), $attributes['type'], $attributes['alias']]);
            }
        }
    }
}