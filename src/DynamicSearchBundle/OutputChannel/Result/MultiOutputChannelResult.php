<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class MultiOutputChannelResult implements MultiOutputChannelResultInterface
{
    /**
     * @var RuntimeOptionsProviderInterface
     */
    protected $runtimeOptionsProvider;

    /**
     * @var array
     */
    protected $results;

    /**
     * @param array|OutputChannelResultInterface[] $results
     * @param RuntimeOptionsProviderInterface      $runtimeOptionsProvider
     */
    public function __construct(array $results, RuntimeOptionsProviderInterface $runtimeOptionsProvider)
    {
        $this->results = $results;
        $this->runtimeOptionsProvider = $runtimeOptionsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeOptionsProvider()
    {
        return $this->runtimeOptionsProvider;
    }
}
