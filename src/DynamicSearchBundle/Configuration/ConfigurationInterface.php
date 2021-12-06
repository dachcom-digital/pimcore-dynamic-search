<?php

namespace DynamicSearchBundle\Configuration;

interface ConfigurationInterface
{
    public const BUNDLE_PATH = PIMCORE_PRIVATE_VAR . '/bundles/DynamicSearchBundle';

    public function get(string $slot): mixed;
}
