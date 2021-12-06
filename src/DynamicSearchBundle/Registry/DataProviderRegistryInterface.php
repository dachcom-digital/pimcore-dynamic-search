<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataProviderRegistryInterface
{
    public function has(string $identifier): bool;

    public function get(string $identifier): ?DataProviderInterface;
}
