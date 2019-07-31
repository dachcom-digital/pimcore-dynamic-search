<?php

namespace DynamicSearchBundle\Filter\Definition;

interface FilterDefinitionBuilderInterface
{
    /**
     * @param string      $contextName
     * @param string      $outputChannelName
     * @param string|null $parentOutputChannelName
     *
     * @return bool
     */
    public function isApplicable(string $contextName, string $outputChannelName, string $parentOutputChannelName = null);

    /**
     * @param FilterDefinitionInterface $definition
     *
     * @return FilterDefinitionInterface
     */
    public function buildDefinition(FilterDefinitionInterface $definition);
}
