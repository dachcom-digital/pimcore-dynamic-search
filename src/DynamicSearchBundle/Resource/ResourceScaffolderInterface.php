<?php

namespace DynamicSearchBundle\Resource;

use DynamicSearchBundle\Context\ContextDefinitionInterface;

interface ResourceScaffolderInterface
{
    public function isApplicable(mixed $resource): bool;

    public function isBaseResource(mixed $resource): bool;

    public function setup(ContextDefinitionInterface $contextDefinition, mixed $resource): array;
}
