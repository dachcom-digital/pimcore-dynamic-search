<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;

interface ContextGuardRegistryInterface
{
    /**
     * @return ContextGuardInterface[]
     */
    public function getAllGuards(): array;
}
