<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

interface MultiOutputChannelResultInterface
{
    /**
     * @return array|OutputChannelResultInterface[]
     */
    public function getResults();

    /**
     * @return RuntimeOptionsProviderInterface
     */
    public function getRuntimeOptionsProvider();
}
