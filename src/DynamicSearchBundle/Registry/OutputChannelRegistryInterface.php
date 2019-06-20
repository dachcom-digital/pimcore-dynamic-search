<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

interface OutputChannelRegistryInterface
{
    /**
     * @param string $type
     * @param string $identifier
     *
     * @return bool
     */
    public function hasOutputChannel(string $type, string $identifier);

    /**
     * @param string $type
     * @param string $identifier
     *
     * @return OutputChannelInterface
     */
    public function getOutputChannel(string $type, string $identifier);

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
     * @param string $outputProvider
     * @param string $action
     *
     * @return bool
     */
    public function hasOutputChannelModifierAction(string $outputProvider, string $action);

    /**
     * @param string $outputProvider
     * @param string $action
     *
     * @return array|OutputChannelModifierActionInterface[]
     */
    public function getOutputChannelModifierAction(string $outputProvider, string $action);

    /**
     * @param string $outputProvider
     * @param string $filter
     *
     * @return bool
     */
    public function hasOutputChannelModifierFilter(string $outputProvider, string $filter);

    /**
     * @param string $outputProvider
     * @param string $filter
     *
     * @return OutputChannelModifierFilterInterface
     */
    public function getOutputChannelModifierFilter(string $outputProvider, string $filter);

}