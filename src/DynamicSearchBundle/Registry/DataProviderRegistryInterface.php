<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataProviderRegistryInterface
{
    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias);

    /**
     * @param string $alias
     *
     * @return DataProviderInterface
     */
    public function get($alias);
}