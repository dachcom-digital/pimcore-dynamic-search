<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexProviderRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier);

    /**
     * @param string $identifier
     *
     * @return IndexProviderInterface
     */
    public function get(string $identifier);
}
