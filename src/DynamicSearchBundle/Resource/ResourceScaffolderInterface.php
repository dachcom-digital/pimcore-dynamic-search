<?php

namespace DynamicSearchBundle\Resource;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ResourceScaffolderInterface
{
    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isApplicable($resource): bool;

    /**
     * @param mixed $resource
     *
     * @return bool
     */
    public function isBaseResource($resource);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @return array
     */
    public function setup(ContextDefinitionInterface $contextDefinition, $resource): array;
}
