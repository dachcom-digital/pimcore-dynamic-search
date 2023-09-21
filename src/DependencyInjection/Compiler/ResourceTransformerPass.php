<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\TransformerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ResourceTransformerPass implements CompilerPassInterface
{
    public const RESOURCE_SCAFFOLDER_TAG = 'dynamic_search.resource.scaffolder';
    public const RESOURCE_FIELD_TRANSFORMER = 'dynamic_search.resource.field_transformer';

    public function process(ContainerBuilder $container)
    {
        $this->processResourceScaffolder($container);
        $this->processResourceFieldTransformer($container);
    }

    public function processResourceScaffolder(ContainerBuilder $container): void
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        $services = [];
        foreach ($container->findTaggedServiceIds(self::RESOURCE_SCAFFOLDER_TAG, true) as $serviceId => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $alias = isset($attributes[0]['identifier']) ? $attributes[0]['identifier'] : null;
            $dataProvider = isset($attributes[0]['data_provider']) ? $attributes[0]['data_provider'] : null;
            $services[$priority][] = [new Reference($serviceId), $serviceId, $alias, $dataProvider];
        }

        if (count($services) === 0) {
            return;
        }

        krsort($services);
        $services = array_merge(...$services);

        foreach ($services as $service) {
            $transformerRegistryDefinition->addMethodCall('registerResourceScaffolder', [$service[0], $service[1], $service[2], $service[3]]);
        }
    }

    public function processResourceFieldTransformer(ContainerBuilder $container): void
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        foreach ($container->findTaggedServiceIds(self::RESOURCE_FIELD_TRANSFORMER, true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = isset($attributes['identifier']) ? $attributes['identifier'] : null;
                $transformerRegistryDefinition->addMethodCall(
                    'registerResourceFieldTransformer',
                    [new Reference($id), $id, $alias, $attributes['resource_scaffolder']]
                );
            }
        }
    }
}
