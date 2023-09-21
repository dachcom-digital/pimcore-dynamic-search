<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class MultiOutputChannelResult implements MultiOutputChannelResultInterface
{
    public function __construct(
        protected array $results,
        protected RuntimeQueryProviderInterface $runtimeQueryProvider
    ) {
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface
    {
        return $this->runtimeQueryProvider;
    }
}
