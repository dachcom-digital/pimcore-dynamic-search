<?php

namespace DynamicSearchBundle\OutputChannel\Result;

use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

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
     * @return RuntimeQueryProviderInterface
     */
    public function getRuntimeQueryProvider();

    /**
     * @return \ArrayObject
     */
    public function getRuntimeOptions();
}
