<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

interface IndexManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
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
     *
     * @return IndexFieldInterface[]
     */
    public function getIndexFieldsOfIndexProvider(ContextDataInterface $contextData);
}
