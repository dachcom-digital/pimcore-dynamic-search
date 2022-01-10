<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Guard\ContextGuardInterface;
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

        $contextDefinitionFactory = $container->getDefinition(ContextDefinitionFactory::class);

        foreach ($contextConfig as $contextName => $contextConfigNode) {
            $contextDefinitionFactory->addMethodCall('addContextConfig', [$contextName, $contextConfigNode]);
        }
    }
}
