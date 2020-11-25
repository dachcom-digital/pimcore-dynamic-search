<?php

namespace DynamicSearchBundle\Factory;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ContextDefinitionFactoryInterface
{
    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextDefinitionInterface|null
     */
    public function createSingle(string $contextName, string $dispatchType, array $runtimeValues = []);

    /**
     * @param string $dispatchType
     * @param array  $runtimeValues
     *
     * @return ContextDefinitionInterface[]
     */
    public function createStack(string $dispatchType, array $runtimeValues = []);
}
