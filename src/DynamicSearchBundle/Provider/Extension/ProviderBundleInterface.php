<?php

namespace DynamicSearchBundle\Provider\Extension;

interface ProviderBundleInterface
{
    /**
     * @return string
     */
    public function getProviderName(): string;
}
