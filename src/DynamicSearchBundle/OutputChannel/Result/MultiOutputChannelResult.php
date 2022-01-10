<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class MultiOutputChannelResult implements MultiOutputChannelResultInterface
{
    protected array $results;
    protected RuntimeQueryProviderInterface $runtimeQueryProvider;

    public function __construct(array $results, RuntimeQueryProviderInterface $runtimeQueryProvider)
    {
        $this->results = $results;
        $this->runtimeQueryProvider = $runtimeQueryProvider;
    }

    public function getResults():array
    {
        return $this->results;
    }

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface
    {
        return $this->runtimeQueryProvider;
    }
}
