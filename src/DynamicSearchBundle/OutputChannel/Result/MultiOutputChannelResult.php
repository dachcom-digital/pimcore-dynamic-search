<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class MultiOutputChannelResult implements MultiOutputChannelResultInterface
{
    protected RuntimeQueryProviderInterface $runtimeQueryProvider;
    protected array $results;

    public function __construct(array $results, RuntimeQueryProviderInterface $runtimeQueryProvider)
    {
        $this->results = $results;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getRuntimeQueryProvider()
    {
        return $this->runtimeQueryProvider;
    }
}
