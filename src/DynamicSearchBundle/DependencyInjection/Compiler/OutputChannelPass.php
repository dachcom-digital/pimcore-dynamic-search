<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\DependencyInjection\Compiler\Helper\OptionsResolverValidator;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Registry\OutputChannelRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OutputChannelPass implements CompilerPassInterface
{
    public const OUTPUT_CHANNEL_TAG = 'dynamic_search.output_channel';
    public const OUTPUT_CHANNEL_RUNTIME_QUERY_BUILDER_TAG = 'dynamic_search.output_channel.runtime_query_provider';
    public const OUTPUT_CHANNEL_RUNTIME_OPTIONS_BUILDER_TAG = 'dynamic_search.output_channel.runtime_options_builder';
    public const OUTPUT_CHANNEL_MODIFIER_ACTION_TAG = 'dynamic_search.output_channel.modifier.action';
    public const OUTPUT_CHANNEL_MODIFIER_FILTER_TAG = 'dynamic_search.output_channel.modifier.filter';

    public function process(ContainerBuilder $container): void
    {
        $outputChannelServices = [];
        $definition = $container->getDefinition(OutputChannelRegistry::class);

        //
        // dynamic_search.output_channel
        //

        $serviceDefinitionStack = [];
        foreach ($container->findTaggedServiceIds(self::OUTPUT_CHANNEL_TAG, true) as $id => $tags) {
            foreach ($tags as $attributes) {

                $alias = $attributes['identifier'] ?? null;
                $serviceName = $alias ?? $id;

                $outputChannelServices[] = $serviceName;
                $serviceDefinitionStack[] = ['serviceName' => $serviceName, 'id' => $id];
                $definition->addMethodCall('registerOutputChannelService', [new Reference($id), $id, $alias]);
            }
        }

        $this->validateOutputChannelOptions($container, $serviceDefinitionStack);

        //
        // dynamic_search.output_channel.runtime_query_provider
        //

        foreach ($container->findTaggedServiceIds(self::OUTPUT_CHANNEL_RUNTIME_QUERY_BUILDER_TAG, true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = $attributes['identifier'] ?? null;
                $definition->addMethodCall('registerOutputChannelRuntimeQueryProvider', [new Reference($id), $id, $alias]);
            }
        }

        //
        // dynamic_search.output_channel.runtime_options_builder
        //

        foreach ($container->findTaggedServiceIds(self::OUTPUT_CHANNEL_RUNTIME_OPTIONS_BUILDER_TAG, true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = $attributes['identifier'] ?? null;
                $definition->addMethodCall('registerOutputChannelRuntimeOptionsBuilder', [new Reference($id), $id, $alias]);
            }
        }

        //
        // dynamic_search.output_channel.modifier.action
        //

        $validModifierChannelServices = array_merge(['all'], $outputChannelServices);

        $outputChannelModifierActionServices = [];
        foreach ($container->findTaggedServiceIds(self::OUTPUT_CHANNEL_MODIFIER_ACTION_TAG, true) as $id => $tags) {

            if (count($outputChannelServices) === 0) {
                continue;
            }

            foreach ($tags as $attributes) {
                $priority = $attributes['priority'] ?? 0;
                $outputChannelService = $attributes['output_channel_service_identifier'] ?? 'all';
                if (!in_array($outputChannelService, $validModifierChannelServices, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            '"%s" is an invalid output channel type for filter. Channel needs to be one of %s',
                            $outputChannelService,
                            implode(', ', $validModifierChannelServices)
                        )
                    );
                }

                if ($outputChannelService === 'all') {
                    foreach ($outputChannelServices as $channelService) {
                        $outputChannelModifierActionServices[$priority][] = [
                            new Reference($id),
                            $channelService,
                            $attributes['action']
                        ];
                    }
                } else {
                    $outputChannelModifierActionServices[$priority][] = [
                        new Reference($id),
                        $outputChannelService,
                        $attributes['action']
                    ];
                }
            }
        }

        krsort($outputChannelModifierActionServices);
        if (count($outputChannelModifierActionServices) > 0) {
            $outputChannelModifierActionServices = array_merge(...$outputChannelModifierActionServices);
            foreach ($outputChannelModifierActionServices as $serviceData) {
                $definition->addMethodCall('registerOutputChannelModifierAction', $serviceData);
            }
        }

        //
        // dynamic_search.output_channel.modifier.filter
        //

        $outputChannelModifierFilterServices = [];
        foreach ($container->findTaggedServiceIds(self::OUTPUT_CHANNEL_MODIFIER_FILTER_TAG, true) as $id => $tags) {

            if (count($outputChannelServices) === 0) {
                continue;
            }

            foreach ($tags as $attributes) {
                $priority = $attributes['priority'] ?? 0;
                $outputChannelService = $attributes['output_channel_service_identifier'] ?? 'all';
                if (!in_array($outputChannelService, $outputChannelServices, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            '"%s" is an invalid output channel type for filter. Channel needs to be one of %s',
                            $outputChannelService,
                            implode(', ', $outputChannelServices)
                        )
                    );
                }

                if ($outputChannelService === 'all') {
                    foreach ($outputChannelServices as $channelService) {
                        $outputChannelModifierFilterServices[$priority][] = [
                            new Reference($id),
                            $channelService,
                            $attributes['filter']
                        ];
                    }
                } else {
                    $outputChannelModifierFilterServices[$priority][] = [
                        new Reference($id),
                        $outputChannelService,
                        $attributes['filter']
                    ];
                }
            }
        }

        krsort($outputChannelModifierFilterServices);
        if (count($outputChannelModifierFilterServices) > 0) {
            $dispatchedFilter = [];
            $outputChannelModifierFilterServices = array_merge(...$outputChannelModifierFilterServices);
            foreach ($outputChannelModifierFilterServices as $serviceData) {
                // highest priority filter wins.
                $key = sprintf('%s_%s', $serviceData[1], $serviceData[2]);
                if (in_array($key, $dispatchedFilter, true)) {
                    continue;
                }
                $dispatchedFilter[] = $key;
                $definition->addMethodCall('registerOutputChannelModifierFilter', $serviceData);
            }
        }
    }

    protected function validateOutputChannelOptions(ContainerBuilder $container, array $serviceDefinitionStack): void
    {
        if (!$container->hasParameter('dynamic_search.context.full_configuration')) {
            return;
        }

        $validator = new OptionsResolverValidator();
        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);
        $contextConfiguration = $container->getParameter('dynamic_search.context.full_configuration');

        foreach ($contextConfiguration as $contextName => &$contextConfig) {

            if (!isset($contextConfig['output_channels']) || !is_array($contextConfig['output_channels'])) {
                continue;
            }

            foreach ($contextConfig['output_channels'] as $outputChannelName => &$outputChannelConfig) {

                $contextService = [
                    'serviceName' => $outputChannelConfig['service'] ?? null,
                    'options'     => $outputChannelConfig['options'] ?? null
                ];

                $outputChannelConfig['options'] = $validator->validate($container, $contextService, $serviceDefinitionStack);
            }

            $contextDefinitionFactory->addMethodCall('replaceContextConfig', [$contextName, $contextConfig]);
        }

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfiguration);
    }
}
