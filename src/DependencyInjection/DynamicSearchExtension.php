<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\Configuration\Configuration as BundleConfiguration;
use DynamicSearchBundle\DependencyInjection\Compiler\ContextGuardPass;
use DynamicSearchBundle\DependencyInjection\Compiler\DefinitionBuilderPass;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Factory\ContextDefinitionFactory;
use DynamicSearchBundle\Filter\Definition\FilterDefinitionBuilderInterface;
use DynamicSearchBundle\Guard\ContextGuardInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class DynamicSearchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../../config']));
        $loader->load('services.yaml');

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
