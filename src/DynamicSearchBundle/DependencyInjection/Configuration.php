<?php

namespace DynamicSearchBundle\DependencyInjection;

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
                ->arrayNode('context')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()

                            ->scalarNode('data_provider')
                                ->defaultNull()
                                ->info('Data Provider')
                            ->end()

                            ->scalarNode('index_provider')
                                ->defaultNull()
                                ->info('Index Provider')
                            ->end()

                            ->arrayNode('data_provider_options')
                            ->isRequired()
                                ->variablePrototype()->defaultValue([])->end()
                            ->end()
                            ->arrayNode('index_provider_options')
                            ->isRequired()
                                ->variablePrototype()->defaultValue([])->end()
                            ->end()
                            ->arrayNode('data_transformer_options')
                            ->isRequired()
                                ->variablePrototype()->defaultValue([])->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
