<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param string               $providerBehaviour
     *
     * @return DataProviderInterface
     *
     * @throws ProviderException
     */
    public function getDataProvider(ContextDataInterface $contextData, string $providerBehaviour);
}
