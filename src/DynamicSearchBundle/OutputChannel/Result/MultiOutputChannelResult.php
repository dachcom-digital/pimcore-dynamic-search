<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class MultiOutputChannelResult implements MultiOutputChannelResultInterface
{
    /**
     * @var RuntimeQueryProviderInterface
     */
    protected $runtimeQueryProvider;

    /**
     * @var array
     */
    protected $results;

    /**
     * @param array|OutputChannelResultInterface[] $results
     * @param RuntimeQueryProviderInterface        $runtimeQueryProvider
     */
    public function __construct(array $results, RuntimeQueryProviderInterface $runtimeQueryProvider)
    {
        $this->results = $results;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
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
    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }
}
