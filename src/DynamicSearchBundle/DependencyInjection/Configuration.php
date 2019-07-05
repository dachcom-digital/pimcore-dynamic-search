<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $validOutputChannels = ContextDataInterface::AVAILABLE_OUTPUT_CHANNEL_TYPES;

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dynamic_search');

        $rootNode
            ->children()
                ->arrayNode('context')

                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()

                            ->arrayNode('resource')
                                ->children()
                                    ->scalarNode('normalizer_service')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('id_builder_service')
                                        ->defaultNull()
                                    ->end()
                                    ->arrayNode('options')
                                    ->isRequired()
                                        ->variablePrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('index_document_definition')
                                ->children()
                                    ->scalarNode('service')
                                        ->defaultNull()
                                    ->end()
                                    ->arrayNode('options')
                                    ->isRequired()
                                        ->variablePrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('index_provider')
                                ->children()
                                    ->scalarNode('service')
                                        ->defaultNull()
                                    ->end()
                                    ->arrayNode('options')
                                    ->isRequired()
                                        ->variablePrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('data_provider')
                                ->children()
                                    ->scalarNode('service')
                                        ->defaultNull()
                                    ->end()
                                    ->scalarNode('resource_normalizer')
                                        ->defaultNull()
                                    ->end()
                                    ->arrayNode('options')
                                    ->isRequired()
                                        ->variablePrototype()->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()

                            ->arrayNode('output_channels')
                                ->useAttributeAsKey('name')
                                ->validate()
                                    ->ifTrue(function ($values) use ($validOutputChannels) {
                                        return $this->checkValidOutputChannel($values, $validOutputChannels);
                                    })
                                    ->thenInvalid(sprintf('Invalid output channel. use one of %s', join(', ', $validOutputChannels)))
                                ->end()
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('service')
                                            ->defaultNull()
                                        ->end()
                                        ->scalarNode('runtime_options_provider')
                                            ->defaultValue('default')
                                        ->end()
                                        ->arrayNode('options')
                                            ->variablePrototype()->defaultValue([])->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()

                        ->end()
                    ->end()

                ->end()
            ->end();

        return $treeBuilder;
    }

    protected function checkValidOutputChannel($givenValues, $validOutputChannels)
    {
        foreach (array_keys($givenValues) as $channel) {
            if (!in_array($channel, $validOutputChannels)) {
                return true;
            }
        }

        return false;
    }
}
