<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Provider\OutputChannel\OutputChannelInterface;

interface IndexManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
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
     * @param string               $type
     *
     * @return OutputChannelInterface
     */
    public function getIndexProviderOutputChannel(ContextDataInterface $contextData, string $type);
}
