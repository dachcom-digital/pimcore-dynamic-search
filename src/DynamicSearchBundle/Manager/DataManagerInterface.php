<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;

interface DataManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return DataProviderInterface
     */
    public function getDataProvider(ContextDataInterface $contextData);
}
