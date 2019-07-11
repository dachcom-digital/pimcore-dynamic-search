<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\TransformerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TransformerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processDispatchTransformer($container);
        $this->processFieldTransformer($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processDispatchTransformer(ContainerBuilder $container)
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        $services = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.transformer.document', true) as $serviceId => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $identifier = isset($attributes[0]['identifier']) ? $attributes[0]['identifier'] : 0;
            $services[$priority][] = [new Reference($serviceId), $identifier];
        }

        if (count($services) === 0) {
            return;
        }

        krsort($services);
        $services = \call_user_func_array('array_merge', $services);

        foreach ($services as $service) {
            $transformerRegistryDefinition->addMethodCall('registerDocumentTransformer', [$service[0], $service[1]]);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processFieldTransformer(ContainerBuilder $container)
    {
        $transformerRegistryDefinition = $container->getDefinition(TransformerRegistry::class);

        foreach ($container->findTaggedServiceIds('dynamic_search.transformer.field', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $transformerRegistryDefinition->addMethodCall('registerFieldTransformer',
                    [new Reference($id), $attributes['identifier'], $attributes['data_transformer']]);
            }
        }
    }

}