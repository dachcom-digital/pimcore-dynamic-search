<?php

namespace DynamicSearchBundle\Filter\Definition;

interface FilterDefinitionBuilderInterface
{
    /**
     * @param string $contextName
     * @param string $outputChannelName
     *
     * @return bool
     */
    public function isApplicable(string $contextName, string $outputChannelName);

    /**
     * @param FilterDefinitionInterface $definition
     *
     * @return FilterDefinitionInterface
     */
    public function buildDefinition(FilterDefinitionInterface $definition);
}
