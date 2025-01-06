<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;
use DynamicSearchBundle\Registry\Storage\RegistryStorage;

class OutputChannelRegistry implements OutputChannelRegistryInterface
{
    protected RegistryStorage $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    public function registerOutputChannelService(OutputChannelInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, OutputChannelInterface::class, 'outputChannel', $identifier, $alias);
    }

    public function registerOutputChannelRuntimeQueryProvider(RuntimeQueryProviderInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, RuntimeQueryProviderInterface::class, 'runtimeQueryProvider', $identifier, $alias);
    }

    public function registerOutputChannelRuntimeOptionsBuilder(RuntimeOptionsBuilderInterface $service, string $identifier, ?string $alias): void
    {
        $this->registryStorage->store($service, RuntimeOptionsBuilderInterface::class, 'runtimeOptionsBuilder', $identifier, $alias);
    }

    public function registerOutputChannelModifierAction(OutputChannelModifierActionInterface $service, string $outputChannelService, string $action): void
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelService, $action);
        $this->registryStorage->store($service, OutputChannelModifierActionInterface::class, $namespace, null, null, true);
    }

    public function registerOutputChannelModifierFilter(OutputChannelModifierFilterInterface $service, string $outputChannelService, string $filter)
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelService);
        $this->registryStorage->store($service, OutputChannelModifierFilterInterface::class, $namespace, $filter);
    }

    public function hasOutputChannelService(string $identifier): bool
    {
        return $this->registryStorage->has('outputChannel', $identifier);
    }

    public function getOutputChannelService(string $identifier): ?OutputChannelInterface
    {
        return $this->registryStorage->get('outputChannel', $identifier);
    }

    public function hasOutputChannelRuntimeQueryProvider(string $identifier): bool
    {
        return $this->registryStorage->has('runtimeQueryProvider', $identifier);
    }

    public function getOutputChannelRuntimeQueryProvider(string $identifier): ?RuntimeQueryProviderInterface
    {
        return $this->registryStorage->get('runtimeQueryProvider', $identifier);
    }

    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier): bool
    {
        return $this->registryStorage->has('runtimeOptionsBuilder', $identifier);
    }

    public function getOutputChannelRuntimeOptionsBuilder(string $identifier): ?RuntimeOptionsBuilderInterface
    {
        return $this->registryStorage->get('runtimeOptionsBuilder', $identifier);
    }

    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action): bool
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelServiceName, $action);

        return $this->registryStorage->hasOneByNamespace($namespace);
    }

    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelServiceName, $action);

        return $this->registryStorage->getByNamespace($namespace);
    }

    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): bool
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelServiceName);

        return $this->registryStorage->has($namespace, $filter);
    }

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): ?OutputChannelModifierFilterInterface
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelServiceName);

        return $this->registryStorage->get($namespace, $filter);
    }
}
