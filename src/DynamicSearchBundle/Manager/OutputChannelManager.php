<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Registry\OutputChannelRegistryInterface;

class OutputChannelManager implements OutputChannelManagerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OutputChannelRegistryInterface
     */
    protected $outputChannelRegistry;

    /**
     * @param LoggerInterface                $logger
     * @param ConfigurationInterface         $configuration
     * @param OutputChannelRegistryInterface $outputChannelRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        OutputChannelRegistryInterface $outputChannelRegistry
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->outputChannelRegistry = $outputChannelRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannel(ContextDataInterface $contextData, string $outputChannel)
    {
        $outputChannelServiceName = $contextData->getOutputChannelServiceName($outputChannel);

        // output channel is disabled by default.
        if ($outputChannelServiceName === null) {
            return null;
        }

        if (!is_string($outputChannelServiceName)) {
            throw new ProviderException(sprintf('Invalid requested index output channel service "%s"', $outputChannel));
        }

        if (!$this->outputChannelRegistry->hasOutputChannel($outputChannel, $outputChannelServiceName)) {
            return null;
        }

        $outputChannel = $this->outputChannelRegistry->getOutputChannel($outputChannel, $outputChannelServiceName);

        return $outputChannel;

    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelRuntimeOptionsProvider(string $provider)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelRuntimeOptionsProvider($provider)) {
            return [];
        }

        return $this->outputChannelRegistry->getOutputChannelRuntimeOptionsProvider($provider);
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelModifierAction(string $outputProvider, string $outputChannel, string $action)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierAction($outputProvider, $outputChannel, $action)) {
            return [];
        }

        return $this->outputChannelRegistry->getOutputChannelModifierAction($outputProvider, $outputChannel, $action);
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelModifierFilter(string $outputProvider, string $outputChannel, string $filter)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierFilter($outputProvider, $outputChannel, $filter)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelModifierFilter($outputProvider, $outputChannel, $filter);
    }
}
