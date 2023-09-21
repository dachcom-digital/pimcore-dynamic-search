<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;
use DynamicSearchBundle\Registry\OutputChannelRegistryInterface;

class OutputChannelManager implements OutputChannelManagerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ConfigurationInterface $configuration,
        protected OutputChannelRegistryInterface $outputChannelRegistry
    ) {
    }

    public function getOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannelName): ?OutputChannelInterface
    {
        $outputChannelServiceName = $contextDefinition->getOutputChannelServiceName($outputChannelName);

        // output channel is disabled by default.
        if ($outputChannelServiceName === null) {
            return null;
        }

        if (!is_string($outputChannelServiceName)) {
            throw new ProviderException(sprintf('Invalid requested index output channel service "%s"', $outputChannelName));
        }

        if (!$this->outputChannelRegistry->hasOutputChannelService($outputChannelServiceName)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelService($outputChannelServiceName);
    }

    public function getOutputChannelRuntimeQueryProvider(string $provider): ?RuntimeQueryProviderInterface
    {
        if (!$this->outputChannelRegistry->hasOutputChannelRuntimeQueryProvider($provider)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelRuntimeQueryProvider($provider);
    }

    public function getOutputChannelRuntimeOptionsBuilder(string $provider): ?RuntimeOptionsBuilderInterface
    {
        if (!$this->outputChannelRegistry->hasOutputChannelRuntimeOptionsBuilder($provider)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelRuntimeOptionsBuilder($provider);
    }

    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierAction($outputChannelServiceName, $action)) {
            return [];
        }

        return $this->outputChannelRegistry->getOutputChannelModifierAction($outputChannelServiceName, $action);
    }

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): ?OutputChannelModifierFilterInterface
    {
        if (!$this->outputChannelRegistry->hasOutputChannelModifierFilter($outputChannelServiceName, $filter)) {
            return null;
        }

        return $this->outputChannelRegistry->getOutputChannelModifierFilter($outputChannelServiceName, $filter);
    }
}
