<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

interface OutputChannelResultInterface
{
    /**
     * @return string
     */
    public function getContextName();

    /**
     * @return string
     */
    public function getOutputChannelName();

    /**
     * @return array
     */
    public function getFilter();

    /**
     * @return RuntimeOptionsProviderInterface
     */
    public function getRuntimeOptionsProvider();
}
