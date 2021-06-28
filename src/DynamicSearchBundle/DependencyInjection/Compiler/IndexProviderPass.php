<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\IndexProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class IndexProviderPass implements CompilerPassInterface
{
    public const INDEX_PROVIDER_TAG = 'dynamic_search.index_provider';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(IndexProviderRegistry::class);

        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds(self::INDEX_PROVIDER_TAG, true) as $id => $tags) {
            foreach ($tags as $attributes) {

                $alias = isset($attributes['identifier']) ? $attributes['identifier'] : null;
                $serviceName = $alias !== null ? $alias : $id;

                $serviceDefinitionStack[] = ['serviceName' => $serviceName, 'id' => $id];
                $definition->addMethodCall('register', [new Reference($id), $id, $alias]);
            }
        }

        $this->validateOptions($container, $serviceDefinitionStack);
    }

    protected function validateOptions(ContainerBuilder $container, array $serviceDefinitionStack): void
    {
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return;
        }

        $validator = new OptionsResolverValidator();
        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);
        $contextConfiguration = $container->getParameter('dynamic_search.context.full_configuration');

        foreach ($contextConfiguration as $contextName => &$contextConfig) {

            $contextService = [
                'serviceName' => $contextConfig['index_provider']['service'] ?? null,
                'options'     => $contextConfig['index_provider']['options'] ?? null
            ];

            $contextConfig['index_provider']['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);

            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        unset($contextConfig);

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }
}
