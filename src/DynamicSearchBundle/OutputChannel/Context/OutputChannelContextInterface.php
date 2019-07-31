<?php

namespace DynamicSearchBundle\OutputChannel\Context;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

interface OutputChannelContextInterface
{
    /**
     * @return ContextDataInterface
     */
    public function getContextDefinition();

    /**
     * @return RuntimeOptionsProviderInterface
     */
    public function getRuntimeOptionsProvider();

    /**
     * @return array
     */
    public function getIndexProviderOptions();

    /**
     * @return string
     */
    public function getOutputChannelName();

    /**
     * @return string
     */
    public function getOutputChannelServiceName();
}
