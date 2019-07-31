<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Filter\Definition\FilterDefinition;

interface FilterDefinitionManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param string               $outputChannelName
     * @param string|null          $subOutputChannelName
     *
     * @return FilterDefinition|null
     */
    public function generateFilterDefinition(ContextDataInterface $contextData, string $outputChannelName, ?string $subOutputChannelName);
}
