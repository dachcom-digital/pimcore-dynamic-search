<?php

namespace DynamicSearchBundle\Filter\Definition;

interface FilterDefinitionInterface
{
    public function addFilterDefinition(array $definition): static;
}
