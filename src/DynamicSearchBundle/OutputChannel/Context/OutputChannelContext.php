<?php

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\OutputChannel\Allocator\OutputChannelAllocatorInterface;
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
     * @var OutputChannelAllocatorInterface
     */
    protected $outputChannelAllocator;

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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getIndexProviderOptions()
    {
        return $this->indexProviderOptions;
    }

    /**
     * @param OutputChannelAllocatorInterface $outputChannelAllocator
     */
    public function setOutputChannelAllocator(OutputChannelAllocatorInterface $outputChannelAllocator)
    {
        $this->outputChannelAllocator = $outputChannelAllocator;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelAllocator()
    {
        return $this->outputChannelAllocator;
    }

    /**
     * @param string $outputChannelServiceName
     */
    public function setOutputChannelServiceName(string $outputChannelServiceName)
    {
        $this->outputChannelServiceName = $outputChannelServiceName;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelServiceName()
    {
        return $this->outputChannelServiceName;
    }
}


