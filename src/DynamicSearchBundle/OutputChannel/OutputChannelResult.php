<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    protected $contextName;

    protected $outputChannelServiceName;

    protected $outputChannelName;

    protected $runtimeOptionsProvider;

    protected $result;

    protected $documentFields;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelServiceName
     * @param string                          $outputChannelName
     * @param mixed                           $result
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     * @param array                           $documentFields
     */
    public function __construct(
        $contextName,
        $outputChannelServiceName,
        $outputChannelName,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        $result,
        $documentFields
    ) {
        $this->contextName = $contextName;
        $this->outputChannelServiceName = $outputChannelServiceName;
        $this->outputChannelName = $outputChannelName;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
        $this->result = $result;
        $this->documentFields = $documentFields;
    }

    /**
     * {@inheritDoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelServiceName()
    {
        return $this->outputChannelServiceName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataTransformerFieldDefinitions()
    {
        return $this->documentFields;
    }
}