<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataProviderRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * @param string $identifier
     *
     * @return DataProviderInterface
     */
    public function get($identifier);
}