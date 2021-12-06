<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\ResourceNormalizerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class NormalizerPass implements CompilerPassInterface
{
    public const RESOURCE_NORMALIZER_TAG = 'dynamic_search.resource_normalizer';
    public const DOCUMENT_NORMALIZER_TAG = 'dynamic_search.document_normalizer';

    public function process(ContainerBuilder $container): void
    {
        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds(self::RESOURCE_NORMALIZER_TAG, true) as $id => $tags) {
            $definition = $container->getDefinition(ResourceNormalizerRegistry::class);
            foreach ($tags as $attributes) {

                $alias = $attributes['identifier'] ?? null;
                $serviceName = $alias ?? $id;

                $serviceDefinitionStack[] = ['serviceName' => $serviceName, 'id' => $id];
                $definition->addMethodCall('registerResourceNormalizer', [new Reference($id), $id, $alias, $attributes['data_provider']]);
            }
        }

        $this->validateResourceNormalizerOptions($container, $serviceDefinitionStack);

        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds(self::DOCUMENT_NORMALIZER_TAG, true) as $id => $tags) {
            $definition = $container->getDefinition(ResourceNormalizerRegistry::class);
            foreach ($tags as $attributes) {

                $alias = $attributes['identifier'] ?? null;
                $serviceName = $alias ?? $id;

                $serviceDefinitionStack[] = ['serviceName' => $serviceName, 'id' => $id];
                $definition->addMethodCall('registerDocumentNormalizer', [new Reference($id), $id, $alias, $attributes['index_provider']]);
            }
        }

        $this->validateDocumentNormalizerOptions($container, $serviceDefinitionStack);
    }

    protected function validateResourceNormalizerOptions(ContainerBuilder $container, array $serviceDefinitionStack): void
    {
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return;
        }

        $validator = new OptionsResolverValidator();
        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);
        $contextConfiguration = $container->getParameter('dynamic_search.context.full_configuration');

        foreach ($contextConfiguration as $contextName => &$contextConfig) {

            if (!isset($contextConfig['data_provider']['normalizer'])) {
                continue;
            }

            $contextService = [
                'serviceName' => $contextConfig['data_provider']['normalizer']['service'] ?? null,
                'options'     => $contextConfig['data_provider']['normalizer']['options'] ?? null
            ];

            $contextConfig['data_provider']['normalizer']['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);

            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }

    protected function validateDocumentNormalizerOptions(ContainerBuilder $container, array $serviceDefinitionStack): void
    {
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return;
        }

        $validator = new OptionsResolverValidator();
        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);
        $contextConfiguration = $container->getParameter('dynamic_search.context.full_configuration');

        foreach ($contextConfiguration as $contextName => &$contextConfig) {

            if (!isset($contextConfig['output_channels']) ||!is_array($contextConfig['output_channels'])) {
                continue;
            }

            foreach ($contextConfig['output_channels'] as $outputChannelName => &$outputChannelConfig) {
                $contextService = [
                    'serviceName' => $outputChannelConfig['normalizer']['service'] ?? null,
                    'options'     => $outputChannelConfig['normalizer']['options'] ?? null
                ];

                $outputChannelConfig['normalizer']['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);
            }

            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }
}
