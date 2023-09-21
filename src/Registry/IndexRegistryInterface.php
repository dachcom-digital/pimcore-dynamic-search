<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;

interface IndexRegistryInterface
{
    public function hasFieldForIndexProvider(string $indexProviderName, string $identifier): bool;

    public function getFieldForIndexProvider(string $indexProviderName, string $identifier): ?IndexFieldInterface;

    public function hasFilterForIndexProvider(string $indexProviderName, string $identifier): bool;

    public function getFilterForIndexProvider(string $indexProviderName, string $identifier): ?FilterInterface;
}
