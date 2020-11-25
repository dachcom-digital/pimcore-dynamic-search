<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelManagerInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string               $type
     *
     * @return OutputChannelInterface|null
     *
     * @throws ProviderException
     */
    public function getOutputChannel(ContextDefinitionInterface $contextDefinition, string $type);

    /**
     * @param string $provider
     *
     * @return RuntimeQueryProviderInterface
     */
    public function getOutputChannelRuntimeQueryProvider(string $provider);

    /**
     * @param string $provider
     *
     * @return RuntimeOptionsBuilderInterface
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $provider);

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
     * @return null|OutputChannelModifierFilterInterface
     */
    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter);
}
