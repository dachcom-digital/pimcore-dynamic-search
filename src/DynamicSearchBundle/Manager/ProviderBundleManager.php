<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Provider\Extension\ProviderBundleLocatorInterface;
use DynamicSearchBundle\Provider\Extension\ProviderConfig;

class ProviderBundleManager implements ProviderBundleManagerInterface
{
    /**
     * @var ProviderBundleLocatorInterface
     */
    protected $providerBundleLocator;

    /**
     * @var ProviderConfig
     */
    protected $providerConfig;

    /**
     * @param ProviderBundleLocatorInterface $providerBundleLocator
     * @param ProviderConfig                 $providerConfig
     */
    public function __construct(ProviderBundleLocatorInterface $providerBundleLocator, ProviderConfig $providerConfig)
    {
        $this->providerBundleLocator = $providerBundleLocator;
        $this->providerConfig = $providerConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailableProviderBundles()
    {
        $bundles = $this->providerBundleLocator->findProviderBundles();

        $this->providerConfig->saveConfig($bundles);
    }
}
