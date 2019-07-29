<?php

namespace DynamicSearchBundle\Filter\Definition;

interface FilterDefinitionInterface
{
    /**
     * @param array $definition
     *
     * @return $this
     */
    public function addFilterDefinition(array $definition);
}
