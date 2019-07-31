<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Filter\FilterInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
     *
     * @throws ProviderException
     */
    public function getIndexProvider(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface $contextData
     * @param string               $identifier
     *
     * @return IndexFieldInterface|null
     */
    public function getIndexField(ContextDataInterface $contextData, string $identifier);

    /**
     * @param ContextDataInterface $contextData
     * @param string               $identifier
     *
     * @return FilterInterface|null
     */
    public function getFilter(ContextDataInterface $contextData, string $identifier);
}
