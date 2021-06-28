<?php

namespace DynamicSearchBundle\Configuration;

interface ConfigurationInterface
{
    const BUNDLE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle';

    public function get(string $slot);
}
