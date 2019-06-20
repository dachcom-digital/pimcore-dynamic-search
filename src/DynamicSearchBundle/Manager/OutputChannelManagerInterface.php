<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

interface OutputChannelManagerInterface
{
    /**
     * @param ContextDataInterface $contextData
     * @param string               $type
     *
     * @return OutputChannelInterface|null
     * @throws ProviderException
     */
    public function getOutputChannel(ContextDataInterface $contextData, string $type);

    /**
     * @param string $provider
     *
     * @return RuntimeOptionsProviderInterface
     */
    public function getOutputChannelRuntimeOptionsProvider(string $provider);

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
     * @return null|OutputChannelModifierFilterInterface
     */
    public function getOutputChannelModifierFilter(string $outputProvider, string $filter);
}
