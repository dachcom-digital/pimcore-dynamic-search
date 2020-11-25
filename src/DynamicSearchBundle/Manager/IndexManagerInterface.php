<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return IndexProviderInterface
     *
     * @throws ProviderException
     */
    public function getIndexProvider(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string               $identifier
     *
     * @return IndexFieldInterface|null
     */
    public function getIndexField(ContextDefinitionInterface $contextDefinition, string $identifier);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string               $identifier
     *
     * @return FilterInterface|null
     */
    public function getFilter(ContextDefinitionInterface $contextDefinition, string $identifier);
}
