<?php

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelContext implements OutputChannelContextInterface
{
    /**
     * @var ContextDataInterface
     */
    protected $contextDefinition;

    /**
     * @var RuntimeQueryProviderInterface
     */
    protected $runtimeQueryProvider;

    /**
     * @var \ArrayObject
     */
    protected $runtimeOptions;

    /**
     * @var array
     */
    protected $indexProviderOptions;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var string
     */
    protected $outputChannelServiceName;

    /**
     * @param ContextDataInterface $contextDefinition
     */
    public function setContextDefinition(ContextDataInterface $contextDefinition)
    {
        $this->contextDefinition = $contextDefinition;
    }

    /**
     * @return ContextDataInterface
     */
    public function getContextDefinition()
    {
        return $this->contextDefinition;
    }

    /**
     * @param RuntimeQueryProviderInterface $runtimeQueryProvider
     */
    public function setRuntimeQueryProvider(RuntimeQueryProviderInterface $runtimeQueryProvider)
    {
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    /**
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }

    /**
     * @param \ArrayObject $runtimeOptions
     */
    public function setRuntimeOptions(\ArrayObject $runtimeOptions)
    {
        $this->runtimeOptions = $runtimeOptions;
    }

    /**
     * @return \ArrayObject
     */
    public function getRuntimeOptions()
    {
        return $this->runtimeOptions;
    }

    /**
     * @param array $indexProviderOptions
     */
    public function setIndexProviderOptions(array $indexProviderOptions)
    {
        $this->indexProviderOptions = $indexProviderOptions;
    }

    /**
     * @return array
     */
    public function getIndexProviderOptions()
    {
        return $this->indexProviderOptions;
    }

    /**
     * @param string $outputChannelName
     */
    public function setOutputChannelName(string $outputChannelName)
    {
        $this->outputChannelName = $outputChannelName;
    }

    /**
     * @return string
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * @param string $outputChannelServiceName
     */
    public function setOutputChannelServiceName(string $outputChannelServiceName)
    {
        $this->outputChannelServiceName = $outputChannelServiceName;
    }

    /**
     * @return string
     */
    public function getOutputChannelServiceName()
    {
        return $this->outputChannelServiceName;
    }
}


