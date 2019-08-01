<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Registry\OutputChannelRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OutputChannelPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $outputChannelServices = [];
        $definition = $container->getDefinition(OutputChannelRegistry::class);

        //#
        //# dynamic_search.output_channel
        //#

        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $outputChannelServices[] = $attributes['identifier'];
                $definition->addMethodCall('registerOutputChannelService', [new Reference($id), $attributes['identifier']]);
            }
        }

        //#
        //# dynamic_search.output_channel.runtime_query_provider
        //#

        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.runtime_query_provider', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerOutputChannelRuntimeQueryProvider', [new Reference($id), $attributes['identifier']]);
            }
        }

        //#
        //# dynamic_search.output_channel.runtime_options_builder
        //#

        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.runtime_options_builder', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerOutputChannelRuntimeOptionsBuilder', [new Reference($id), $attributes['identifier']]);
            }
        }

        //#
        //# dynamic_search.output_channel.modifier.action
        //#

        $validModifierChannelServices = array_merge(['all'], $outputChannelServices);

        $outputChannelModifierActionServices = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.modifier.action', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
                $outputChannelService = isset($attributes['output_channel_service_identifier']) ? $attributes['output_channel_service_identifier'] : 'all';
                if (!in_array($outputChannelService, $validModifierChannelServices)) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is an invalid output channel type for filter. Channel needs to be one of %s', $outputChannelService,
                            implode(', ', $validModifierChannelServices))
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
            $outputChannelModifierActionServices = \call_user_func_array('array_merge', $outputChannelModifierActionServices);
            foreach ($outputChannelModifierActionServices as $serviceData) {
                $definition->addMethodCall('registerOutputChannelModifierAction', $serviceData);
            }
        }

        //#
        //# dynamic_search.output_channel.modifier.filter
        //#

        $outputChannelModifierFilterServices = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.modifier.filter', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
                $outputChannelService = isset($attributes['output_channel_service_identifier']) ? $attributes['output_channel_service_identifier'] : 'all';
                if (!in_array($outputChannelService, $outputChannelServices)) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is an invalid output channel type for filter. Channel needs to be one of %s', $outputChannelService,
                            implode(', ', $outputChannelServices))
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
            $outputChannelModifierFilterServices = \call_user_func_array('array_merge', $outputChannelModifierFilterServices);
            foreach ($outputChannelModifierFilterServices as $serviceData) {
                // highest priority filter wins.
                $key = sprintf('%s_%s', $serviceData[1], $serviceData[2]);
                if (in_array($key, $dispatchedFilter)) {
                    continue;
                }
                $dispatchedFilter[] = $key;
                $definition->addMethodCall('registerOutputChannelModifierFilter', $serviceData);
            }
        }
    }
}
