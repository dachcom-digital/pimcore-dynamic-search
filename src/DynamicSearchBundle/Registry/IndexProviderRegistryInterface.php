<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexProviderRegistryInterface
{
    public function has(string $identifier): bool;

    public function get(string $identifier): IndexProviderInterface;
}
