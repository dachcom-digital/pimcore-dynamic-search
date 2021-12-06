<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Guard\ContextGuardInterface;
use DynamicSearchBundle\Paginator\Paginator;
use DynamicSearchBundle\Provider\Extension\ProviderConfig;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use DynamicSearchBundle\Configuration\Configuration as BundleConfiguration;

class DynamicSearchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yml');

        $this->buildAutoconfiguration($container);
        $this->setupConfiguration($container, $config);
        $this->setupProviderBundles($container);
    }

    protected function buildAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(DocumentDefinitionBuilderInterface::class)->addTag(DefinitionBuilderPass::DOCUMENT_DEFINITION_BUILDER);
        $container->registerForAutoconfiguration(FilterDefinitionBuilderInterface::class)->addTag(DefinitionBuilderPass::FILTER_DEFINITION_BUILDER);
        $container->registerForAutoconfiguration(ContextGuardInterface::class)->addTag(ContextGuardPass::CONTEXT_GUARD_TAG);
    }

    protected function setupConfiguration(ContainerBuilder $container, array $config): void
    {
        $contextConfig = $config['context'];

        unset($config['context']);

        $configManagerDefinition = $container->getDefinition(BundleConfiguration::class);
        $configManagerDefinition->addMethodCall('setConfig', [$config]);

        $container->setParameter('dynamic_search.context.full_configuration', $contextConfig);
        $container->setParameter('dynamic_search_default_paginator_class', Paginator::class);

        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);

        foreach ($contextConfig as $contextName => $contextConfigNode) {
            $contextDefinitionFactory->addMethodCall('addContextConfig', [$contextName, $contextConfigNode]);
        }
    }

    protected function setupProviderBundles(ContainerBuilder $container): void
    {
        $providerConfig = new ProviderConfig();

        $providerConfigDefinition = new Definition();
        $providerConfigDefinition->setClass(ProviderConfig::class);

        $container->setDefinition(ProviderConfig::class, $providerConfigDefinition);

        if ($providerConfig->configFileExists()) {
            $container->addResource(new FileResource($providerConfig->locateConfigFile()));
        }
    }
}
