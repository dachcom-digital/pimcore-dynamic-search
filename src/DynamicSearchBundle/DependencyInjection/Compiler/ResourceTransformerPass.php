<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\TransformerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ResourceTransformerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processResourceScaffolder($container);
        $this->processResourceFieldTransformer($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processResourceScaffolder(ContainerBuilder $container)
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        $services = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.resource.scaffolder', true) as $serviceId => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $identifier = isset($attributes[0]['identifier']) ? $attributes[0]['identifier'] : 0;
            $dataProvider = isset($attributes[0]['data_provider']) ? $attributes[0]['data_provider'] : 0;
            $services[$priority][] = [new Reference($serviceId), $identifier, $dataProvider];
        }

        if (count($services) === 0) {
            return;
        }

        krsort($services);
        $services = \call_user_func_array('array_merge', $services);

        foreach ($services as $service) {
            $transformerRegistryDefinition->addMethodCall('registerResourceScaffolder', [$service[0], $service[1], $service[2]]);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processResourceFieldTransformer(ContainerBuilder $container)
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        foreach ($container->findTaggedServiceIds('dynamic_search.resource.field_transformer', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $transformerRegistryDefinition->addMethodCall(
                    'registerResourceFieldTransformer',
                    [new Reference($id), $attributes['identifier'], $attributes['resource_scaffolder']]
                );
            }
        }
    }
}
