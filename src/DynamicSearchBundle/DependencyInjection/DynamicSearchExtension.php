<?php

namespace DynamicSearchBundle\DependencyInjection;

use DynamicSearchBundle\Paginator\Paginator;
use DynamicSearchBundle\Provider\Extension\ProviderConfig;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use DynamicSearchBundle\Configuration\Configuration as BundleConfiguration;

class DynamicSearchExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $providerConfig = new ProviderConfig();
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yml');

        $configManagerDefinition = $container->getDefinition(BundleConfiguration::class);
        $configManagerDefinition->addMethodCall('setConfig', [$config]);

        $container->setParameter('dynamic_search_default_paginator_class', Paginator::class);

        if ($providerConfig->configFileExists()) {
            $container->addResource(new FileResource($providerConfig->locateConfigFile()));
        }

        $container->set(ProviderConfig::class, $providerConfig);
    }
}
