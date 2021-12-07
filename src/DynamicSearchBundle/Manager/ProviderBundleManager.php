<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Provider\Extension\ProviderBundleLocatorInterface;
use DynamicSearchBundle\Provider\Extension\ProviderConfig;

class ProviderBundleManager implements ProviderBundleManagerInterface
{
    protected ProviderBundleLocatorInterface $providerBundleLocator;
    protected ProviderConfig $providerConfig;

    public function __construct(ProviderBundleLocatorInterface $providerBundleLocator, ProviderConfig $providerConfig)
    {
        $this->providerBundleLocator = $providerBundleLocator;
        $this->providerConfig = $providerConfig;
    }

    public function checkAvailableProviderBundles(): void
    {
        $bundles = $this->providerBundleLocator->findProviderBundles();

        $this->providerConfig->saveConfig($bundles);
    }
}
