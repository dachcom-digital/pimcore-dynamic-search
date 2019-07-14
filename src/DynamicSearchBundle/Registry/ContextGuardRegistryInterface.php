<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Guard\ContextGuardInterface;

interface ContextGuardRegistryInterface
{
    /**
     * @return array|ContextGuardInterface[]
     */
    public function getAllGuards();
}
