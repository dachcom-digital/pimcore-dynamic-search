<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class OutputChannelArrayResult implements OutputChannelArrayResultInterface
{
    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var array
     */
    protected $filter;

    /**
     * @var RuntimeOptionsProviderInterface
     */
    protected $runtimeOptionsProvider;

    /**
     * @var array
     */
    protected $result;

    /**
     * @param string                          $contextName
     * @param string                          $outputChannelName
     * @param array                           $filter
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     * @param array                           $result
     */
    public function __construct(
        string $contextName,
        string $outputChannelName,
        array $filter,
        RuntimeOptionsProviderInterface $runtimeOptionsProvider,
        array $result
    ) {
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->filter = $filter;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
        $this->result = $result;
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
    public function getResult()
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }
}
