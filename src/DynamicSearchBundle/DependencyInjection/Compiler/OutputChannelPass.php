<?php

namespace DynamicSearchBundle\DependencyInjection\Compiler;

use DynamicSearchBundle\Context\ContextDataInterface;
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
        $definition = $container->getDefinition(OutputChannelRegistry::class);

        ##
        ## dynamic_search.output_channel
        ##

        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (!in_array($attributes['type'], ContextDataInterface::AVAILABLE_OUTPUT_CHANNEL_TYPES)) {
                    throw new \InvalidArgumentException(sprintf('"%s" is an invalid output channel type. Channel needs to be one of %s', $attributes['type'],
                        implode(', ', ContextDataInterface::AVAILABLE_OUTPUT_CHANNEL_TYPES)));

                }
                $definition->addMethodCall('registerOutputChannel', [new Reference($id), $attributes['type'], $attributes['identifier']]);
            }
        }

        ##
        ## dynamic_search.output_channel.runtime_options_provider
        ##

        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.runtime_options_provider', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('registerOutputChannelRuntimeOptionsProvider', [new Reference($id), $attributes['identifier']]);
            }
        }

        ##
        ## dynamic_search.output_channel.modifier.action
        ##

        $outputChannelModifierActionServices = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.modifier.action', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
                $outputChannelModifierActionServices[$priority][] = [new Reference($id), $attributes['output_provider'], $attributes['action']];
            }
        }

        krsort($outputChannelModifierActionServices);
        if (count($outputChannelModifierActionServices) > 0) {
            $outputChannelModifierActionServices = \call_user_func_array('array_merge', $outputChannelModifierActionServices);
            foreach ($outputChannelModifierActionServices as $serviceData) {
                $definition->addMethodCall('registerOutputChannelModifierAction', $serviceData);
            }
        }

        ##
        ## dynamic_search.output_channel.modifier.filter
        ##

        $dispatchedFilter = [];
        foreach ($container->findTaggedServiceIds('dynamic_search.output_channel.modifier.filter', true) as $id => $tags) {
            foreach ($tags as $attributes) {

                // first filter wins.
                $key = sprintf('%s_%s', $attributes['output_provider'], $attributes['filter']);
                if (in_array($key, $dispatchedFilter)) {
                    continue;
                }

                $dispatchedFilter[] = $key;
                $definition->addMethodCall('registerOutputChannelModifierFilter', [new Reference($id), $attributes['output_provider'], $attributes['filter']]);
            }
        }
    }
}