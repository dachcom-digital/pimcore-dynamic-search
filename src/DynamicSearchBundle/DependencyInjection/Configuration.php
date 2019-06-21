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

                            ->arrayNode('data_transformer')
                                ->children()

                                    ->arrayNode('document')
                                        ->validate()
                                            ->ifTrue(function ($values) {
                                                return !array_key_exists('id', $values);
                                            })
                                            ->thenInvalid('No "id" field for document defined. A ID-Field is required to allow further document modifications.')
                                        ->end()
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('field_transformer')
                                                    ->defaultNull()
                                                ->end()
                                                ->arrayNode('field_transformer_options')
                                                    ->variablePrototype()->defaultValue([])->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('fields')
                                        ->validate()
                                            ->ifTrue(function ($values) {
                                                return array_key_exists('id', $values);
                                            })
                                            ->thenInvalid('"id" is not reserved document field name.')
                                        ->end()
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->children()
                                                ->scalarNode('index_type')
                                                    ->defaultNull()
                                                ->end()
                                                ->scalarNode('field_transformer')
                                                    ->defaultNull()
                                                ->end()
                                                ->arrayNode('field_transformer_options')
                                                    ->variablePrototype()->defaultValue([])->end()
                                                ->end()

                                                ->arrayNode('output_channel')
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->arrayNode('visibility')
                                                            ->validate()
                                                                ->ifTrue(function ($values) use ($validOutputChannels) {
                                                                    return $this->checkValidOutputChannel($values, $validOutputChannels);
                                                                })
                                                                ->thenInvalid(sprintf('Invalid output channel. use one of %s', join(', ', $validOutputChannels)))
                                                            ->end()
                                                            ->beforeNormalization()
                                                                ->ifTrue(function ($values) use ($validOutputChannels) {
                                                                    return count($values) !== count($validOutputChannels);
                                                                })
                                                                ->then(function ($values) use($validOutputChannels) {
                                                                    return array_merge(array_fill_keys($validOutputChannels, true), $values);
                                                                })
                                                            ->end()
                                                            ->useAttributeAsKey('name')
                                                            ->prototype('scalar')->end()
                                                            ->defaultValue(array_fill_keys($validOutputChannels, true))
                                                        ->end()
                                                    ->end()
                                                ->end()

                                            ->end()
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
