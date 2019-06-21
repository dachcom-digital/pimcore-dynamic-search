<?php

namespace DynamicSearchBundle\Configuration;

use DynamicSearchBundle\Context\ContextData;

interface ConfigurationInterface
{
    const CRAWLER_LOG_FILE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle/crawler.log';

    /**
     * @param string $slot
     *
     * @return mixed
     */
    public function get($slot);

    /**
     * @param string $dispatchType
     * @param array  $runtimeOptions
     *
     * @return ContextData[]
     */
    public function getContextDefinitions(string $dispatchType, array $runtimeOptions = []);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $runtimeOptions
     *
     * @return ContextData
     */
    public function getContextDefinition(string $dispatchType, string $contextName, array $runtimeOptions = []);
}
