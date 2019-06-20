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
     * @return ContextData[]
     */
    public function getContextDefinitions();

    /**
     * @param string $contextName
     *
     * @return ContextData
     */
    public function getContextDefinition(string $contextName);
}
