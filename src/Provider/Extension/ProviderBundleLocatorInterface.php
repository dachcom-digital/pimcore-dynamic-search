<?php

namespace DynamicSearchBundle\Provider\Extension;

interface ProviderBundleLocatorInterface
{
    public function findProviderBundles(): array;
}
