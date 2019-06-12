<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexProviderRegistryInterface
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
     * @return IndexProviderInterface
     */
    public function get($alias);
}