<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\Paginator\Adapter\DynamicSearchAdapter;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dynamic_search');

        $rootNode
            ->children()
                ->booleanNode('enable_pimcore_element_listener')->defaultFalse()->end()
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
                                        ->children()
                                            ->arrayNode('always')
                                                ->variablePrototype()->defaultValue([])->end()
                                            ->end()
                                            ->arrayNode('full_dispatch')
                                                ->variablePrototype()->defaultValue([])->end()
                                            ->end()
                                            ->arrayNode('single_dispatch')
                                                ->variablePrototype()->defaultValue([])->end()
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
                                                ->variablePrototype()->defaultValue([])->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                ->end()
                            ->end()

                            ->arrayNode('output_channels')

                                ->useAttributeAsKey('name')
                                ->arrayPrototype()
                                    ->validate()
                                        ->ifTrue(function ($values) {
                                            return $values['multiple'] === true && (!is_array($values['blocks']) || count($values['blocks']) === 0);
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
                                            return $values['multiple'] === true && isset($values['paginator']) && $values['paginator']['enabled'] === true;
                                        })
                                        ->thenInvalid('Unrecognized option "paginator"')
                                    ->end()
                                    ->beforeNormalization()
                                        ->ifTrue(function ($values) {
                                            return $values['multiple'] === true && isset($values['normalizer']) && $values['normalizer']['service'] !== null;
                                        })
                                        ->thenInvalid('Unrecognized option "normalizer"')
                                    ->end()
                                    ->children()
                                        ->scalarNode('service')->defaultNull()->end()
                                        ->booleanNode('multiple')->defaultFalse()->end()
                                        ->booleanNode('internal')->defaultFalse()->end()
                                        ->booleanNode('use_frontend_controller')->defaultFalse()->end()
                                        ->scalarNode('runtime_options_provider')->defaultValue('default')->end()
                                        ->arrayNode('options')
                                            ->variablePrototype()->defaultValue([])->end()
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
                                            ->end()
                                        ->end()
                                        ->arrayNode('normalizer')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('service')
                                                    ->defaultNull()
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
                    ->end()

                ->end()
            ->end();

        return $treeBuilder;
    }
}
