<?php

namespace DynamicSearchBundle\OutputChannel;

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
    public function getOutputChannelServiceName();

    /**
     * @return string
     */
    public function getOutputChannelName();

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @return RuntimeOptionsProviderInterface
     */
    public function getRuntimeOptionsProvider();

    /**
     * @return array
     */
    public function getDataTransformerFieldDefinitions();
}