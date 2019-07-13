<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Index\IndexFieldInterface;

interface IndexFieldRegistryInterface
{
    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return bool
     */
    public function hasForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     * @param string $identifier
     *
     * @return IndexFieldInterface
     */
    public function getForIndexProvider(string $indexProviderName, string $identifier);

    /**
     * @param string $indexProviderName
     *
     * @return IndexFieldInterface[]
     */
    public function getIndexFieldsOfIndexProvider(string $indexProviderName);
}
