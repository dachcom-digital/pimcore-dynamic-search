<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelResult implements OutputChannelResultInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var  \ArrayObject
     */
    protected $runtimeOptions;

    /**
     * @var RuntimeQueryProviderInterface
     */
    protected $runtimeQueryProvider;

    /**
     * @param string                        $contextName
     * @param string                        $outputChannelName
     * @param array                         $filter
     * @param \ArrayObject                  $runtimeOptions
     * @param RuntimeQueryProviderInterface $runtimeQueryProvider
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        array $filter,
        \ArrayObject $runtimeOptions,
        RuntimeQueryProviderInterface $runtimeQueryProvider
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->filter = $filter;
        $this->runtimeOptions = $runtimeOptions;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptions()
    {
        return $this->runtimeOptions;
    }
}
