<?php

namespace DynamicSearchBundle\Configuration;

interface ConfigurationInterface
{
    const CRAWLER_LOG_FILE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle/crawler.log';

    /**
     * @param string $slot
     *
     * @return mixed
     */
    public function get($slot);

    public function getContextDefinitions();

    public function getContextDefinition(string $contextName);
}
