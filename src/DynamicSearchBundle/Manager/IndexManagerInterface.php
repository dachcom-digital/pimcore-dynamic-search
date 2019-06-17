<?php

namespace DynamicSearchBundle\Manager;

use DsLuceneBundle\OutputChannel\AutoCompleteOutputChannelInterface;
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

    /**
     * @param ContextDataInterface $contextData
     * @param string               $type
     *
     * @return AutoCompleteOutputChannelInterface
     */
    public function getIndexProviderOutputChannel(ContextDataInterface $contextData, string $type);
}
