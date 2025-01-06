<?php

namespace DynamicSearchBundle\Configuration;

interface ConfigurationInterface
{
    public function get(string $slot): mixed;
}
