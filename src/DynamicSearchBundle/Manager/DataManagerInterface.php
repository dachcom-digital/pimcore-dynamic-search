<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string               $providerBehaviour
     *
     * @return DataProviderInterface
     *
     * @throws ProviderException
     */
    public function getDataProvider(ContextDefinitionInterface $contextDefinition, string $providerBehaviour);
}
