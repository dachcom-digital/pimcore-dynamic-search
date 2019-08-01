<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasOutputChannelService(string $identifier);

    /**
     * @param string $identifier
     *
     * @return OutputChannelInterface
     */
    public function getOutputChannelService(string $identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasOutputChannelRuntimeQueryProvider(string $identifier);

    /**
     * @param string $identifier
     *
     * @return RuntimeQueryProviderInterface
     */
    public function getOutputChannelRuntimeQueryProvider(string $identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier);

    /**
     * @param string $identifier
     *
     * @return RuntimeOptionsBuilderInterface
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $identifier);

    /**
     * @param string $outputChannelServiceName
     * @param string $action
     *
     * @return bool
     */
    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action);

    /**
     * @param string $outputChannelServiceName
     * @param string $action
     *
     * @return array|OutputChannelModifierActionInterface[]
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action);

    /**
     * @param string $outputChannelServiceName
     * @param string $filter
     *
     * @return bool
     */
    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter);

    /**
     * @param string $outputChannelServiceName
     * @param string $filter
     *
     * @return OutputChannelModifierFilterInterface
     */
    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter);
}
