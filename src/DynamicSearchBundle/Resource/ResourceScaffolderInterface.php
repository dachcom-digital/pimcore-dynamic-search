<?php

namespace DynamicSearchBundle\Resource;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ResourceScaffolderInterface
{
    public function isApplicable($resource): bool;

    public function isBaseResource($resource): bool;

    public function setup(ContextDefinitionInterface $contextDefinition, $resource): array;
}
