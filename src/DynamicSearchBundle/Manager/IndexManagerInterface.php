<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
     */
    public function getIndexProvider(ContextDataInterface $contextData);
}
