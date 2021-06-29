<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexManagerInterface
{
    public function getIndexProvider(ContextDefinitionInterface $contextDefinition): IndexProviderInterface;

    public function getIndexField(
        ContextDefinitionInterface $contextDefinition,
        string $identifier
    ): ?IndexFieldInterface;

    public function getFilter(ContextDefinitionInterface $contextDefinition, string $identifier): ?FilterInterface;
}
