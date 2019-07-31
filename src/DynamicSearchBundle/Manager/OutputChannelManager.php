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
     * {@inheritdoc}
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

        if (!$this->outputChannelRegistry->hasOutputChannelService($outputChannelServiceName)) {
            return null;
        }

        $outputChannel = $this->outputChannelRegistry->getOutputChannelService($outputChannelServiceName);

        return $outputChannel;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeOptionsProvider(string $provider)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelRuntimeOptionsProvider($provider)) {
            return [];
        }

        return $this->outputChannelRegistry->getOutputChannelRuntimeOptionsProvider($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierAction($outputChannelServiceName, $action)) {
            return [];
        }

        return $this->outputChannelRegistry->getOutputChannelModifierAction($outputChannelServiceName, $action);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter)
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierFilter($outputChannelServiceName, $filter)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelModifierFilter($outputChannelServiceName, $filter);
    }
}
