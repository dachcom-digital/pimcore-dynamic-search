<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface MultiOutputChannelResultInterface
{
    /**
     * @return array<int, OutputChannelResultInterface>
     */
    public function getResults(): array;

    /**
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider(): RuntimeQueryProviderInterface;
}
