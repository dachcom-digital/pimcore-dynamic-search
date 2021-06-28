<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface MultiOutputChannelResultInterface
{
    /**
     * @return OutputChannelResultInterface[]
     */
    public function getResults(): array;

    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;
}
