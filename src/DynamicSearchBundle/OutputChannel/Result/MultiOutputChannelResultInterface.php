<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface MultiOutputChannelResultInterface
{
    /**
     * @return array|OutputChannelResultInterface[]
     */
    public function getResults();

    /**
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider();
}
