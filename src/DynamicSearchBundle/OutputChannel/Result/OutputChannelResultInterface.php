<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\Result\Document\DocumentInterface;
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
     * @return array|DocumentInterface[]
     */
    public function getResult();

    /**
     * @return RuntimeOptionsProviderInterface
     */
    public function getRuntimeOptionsProvider();
}