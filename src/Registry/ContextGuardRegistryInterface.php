<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;

interface ContextGuardRegistryInterface
{
    /**
     * @return array<int, ContextGuardInterface>
     */
    public function getAllGuards(): array;
}
