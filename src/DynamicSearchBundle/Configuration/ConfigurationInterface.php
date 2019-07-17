<?php

namespace DynamicSearchBundle\Configuration;

use DynamicSearchBundle\Context\ContextData;

interface ConfigurationInterface
{
    const BUNDLE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle';

    /**
     * @param string $slot
     *
     * @return mixed
     */
    public function get($slot);

    /**
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextData[]
     */
    public function getContextDefinitions(string $dispatchType, array $runtimeValues = []);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextData
     */
    public function getContextDefinition(string $dispatchType, string $contextName, array $runtimeValues = []);
}
