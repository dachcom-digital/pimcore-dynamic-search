<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;

interface IndexRegistryInterface
{
    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFieldForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return IndexFieldInterface
     */
    public function getFieldForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasFilterForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return FilterInterface
     */
    public function getFilterForIndexProvider(string $indexProviderName, string $identifier);
}
