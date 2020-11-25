<?php

namespace DynamicSearchBundle\Configuration;

interface ConfigurationInterface
{
    const BUNDLE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle';

    /**
     * @param string $slot
     *
     * @return mixed
     */
    public function get($slot);
}
