<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

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
    public function hasOutputChannelRuntimeOptionsProvider(string $identifier);

    /**
     * @param string $identifier
     *
     * @return RuntimeOptionsProviderInterface
     */
    public function getOutputChannelRuntimeOptionsProvider(string $identifier);

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
