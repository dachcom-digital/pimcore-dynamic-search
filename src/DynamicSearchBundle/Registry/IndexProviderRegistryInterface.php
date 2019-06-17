<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Provider\OutputChannel\OutputChannelInterface;

interface IndexProviderRegistryInterface
{
    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has(string $alias);

    /**
     * @param string $alias
     *
     * @return IndexProviderInterface
     */
    public function get(string $alias);

    /**
     * @param string $type
     * @param string $alias
     *
     * @return OutputChannelInterface
     */
    public function getOutputChannel(string $type, string $alias);

}