<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\Paginator\Adapter\DynamicSearchAdapter;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dynamic_search');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_pimcore_element_listener')->defaultFalse()->end()
                ->arrayNode('element_inheritance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->enumNode('origin_dispatch')->values(['user', 'all'])->defaultValue('user')->end()
                    ->end()
                ->end()
                ->arrayNode('context')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->append($this->buildContextIndexProviderNode())
                            ->append($this->buildContextDataProviderNode())
                            ->append($this->buildContextOutputChannelsNode())
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function buildContextIndexProviderNode(): NodeDefinition
    {
        $builder = new TreeBuilder('index_provider');
        $node = $builder->getRootNode();

        $node
            ->children()
                ->scalarNode('service')
                    ->defaultNull()
                ->end()
                ->arrayNode('options')
                ->isRequired()
                    ->useAttributeAsKey('name')
                    ->variablePrototype()
                        ->beforeNormalization()->ifNull()->thenUnset()->end()
                        ->defaultValue([])
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function buildContextDataProviderNode(): NodeDefinition
    {
        $builder = new TreeBuilder('data_provider');
        $node = $builder->getRootNode();

        $node
            ->children()
                ->scalarNode('service')
                    ->defaultNull()
                ->end()
                ->arrayNode('options')
                    ->children()
                        ->arrayNode('always')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()
                                ->beforeNormalization()->ifNull()->thenUnset()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                        ->arrayNode('full_dispatch')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()
                                ->beforeNormalization()->ifNull()->thenUnset()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                        ->arrayNode('single_dispatch')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()
                                ->beforeNormalization()->ifNull()->thenUnset()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('normalizer')
                    ->children()
                        ->scalarNode('service')
                            ->isRequired()
                            ->defaultNull()
                        ->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()
                                ->beforeNormalization()->ifNull()->thenUnset()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function buildContextOutputChannelsNode(): NodeDefinition
    {
        $builder = new TreeBuilder('output_channels');
        $node = $builder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('service')->defaultNull()->end()
                    ->booleanNode('multiple')->defaultFalse()->end()
                    ->booleanNode('internal')->defaultFalse()->end()
                    ->scalarNode('view_name')->defaultNull()->end()
                    ->booleanNode('use_frontend_controller')->defaultFalse()->end()
                    ->scalarNode('runtime_query_provider')->defaultValue('default')->end()
                    ->scalarNode('runtime_options_builder')->defaultValue('default')->end()
                    ->arrayNode('options')
                        ->useAttributeAsKey('name')
                        ->variablePrototype()
                            ->beforeNormalization()->ifNull()->thenUnset()->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                    ->arrayNode('blocks')
                        ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('identifier')->end()
                                ->scalarNode('reference')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('paginator')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')
                                ->defaultFalse()
                            ->end()
                            ->scalarNode('adapter_class')
                                ->defaultValue(DynamicSearchAdapter::class)
                            ->end()
                            ->integerNode('max_per_page')
                                ->defaultValue(10)
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('normalizer')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('service')
                                ->defaultNull()
                            ->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('name')
                                ->variablePrototype()
                                    ->beforeNormalization()->ifNull()->thenUnset()->end()
                                    ->defaultValue([])
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function ($values) {
                        return isset($values['multiple']) && $values['multiple'] === true && (!is_array($values['blocks']) || count($values['blocks']) === 0);
                    })
                    ->thenInvalid('"blocks" missing')
                    ->end()
                ->validate()
                    ->ifTrue(function ($values) {
                        return $values['multiple'] === false && !empty($values['blocks']);
                    })
                    ->thenInvalid('Unrecognized option "blocks"')
                ->end()
                ->beforeNormalization()
                    ->ifTrue(function ($values) {
                        return isset($values['multiple'], $values['paginator']) && $values['multiple'] === true && $values['paginator']['enabled'] === true;
                    })
                    ->thenInvalid('Unrecognized multi search  option "paginator"')
                ->end()
                ->beforeNormalization()
                    ->ifTrue(function ($values) {
                        return isset($values['multiple'], $values['normalizer']) && $values['multiple'] === true && $values['normalizer']['service'] !== null;
                    })
                    ->thenInvalid('Unrecognized multi search option "normalizer"')
                ->end()
                ->beforeNormalization()
                    ->ifTrue(function ($values) {
                        return isset($values['multiple'], $values['runtime_options_builder']) && $values['multiple'] === true;
                    })
                    ->thenInvalid('Unrecognized multi search option "runtime_options_builder"')
                ->end()
                ->beforeNormalization()
                    ->ifTrue(function ($values) {
                        return isset($values['use_frontend_controller'], $values['view_name']) && $values['use_frontend_controller'] !== true;
                    })
                    ->thenInvalid('Unrecognized option "view_name" in a non frontend controller based output channel')
                ->end()
                ->beforeNormalization()
                    ->always()
                    ->then(function ($values) {
                        if (!isset($values['use_frontend_controller']) || $values['use_frontend_controller'] !== true) {
                            return $values;
                        }

                        if (isset($values['view_name'])) {
                            return $values;
                        }

                        $values['view_name'] = isset($values['multiple']) && $values['multiple'] === true ? 'multi-list' : 'list';

                        return $values;
                    })
                ->end()

            ->end();

        return $node;
    }

}
