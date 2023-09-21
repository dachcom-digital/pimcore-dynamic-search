<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\State\HealthStateInterface;

interface HealthStateRegistryInterface
{
    /**
     * @return array<int, HealthStateInterface>
     */
    public function all(): array;
}
