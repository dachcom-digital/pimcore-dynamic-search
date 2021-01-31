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
    /**
     * @var RegistryStorage
     */
    protected $registryStorage;

    public function __construct()
    {
        $this->registryStorage = new RegistryStorage();
    }

    /**
     * @param OutputChannelInterface $service
     * @param string                 $identifier
     * @param string|null            $alias
     */
    public function registerOutputChannelService($service, string $identifier, ?string $alias)
    {
        $this->registryStorage->store($service, OutputChannelInterface::class, 'outputChannel', $identifier, $alias);
    }

    /**
     * @param RuntimeQueryProviderInterface $service
     * @param string                        $identifier
     * @param string|null                   $alias
     */
    public function registerOutputChannelRuntimeQueryProvider($service, string $identifier, ?string $alias)
    {
        $this->registryStorage->store($service, RuntimeQueryProviderInterface::class, 'runtimeQueryProvider', $identifier, $alias);
    }

    /**
     * @param RuntimeOptionsBuilderInterface $service
     * @param string                         $identifier
     * @param string|null                    $alias
     */
    public function registerOutputChannelRuntimeOptionsBuilder($service, string $identifier, ?string $alias)
    {
        $this->registryStorage->store($service, RuntimeOptionsBuilderInterface::class, 'runtimeOptionsBuilder', $identifier, $alias);
    }

    /**
     * @param OutputChannelModifierActionInterface $service
     * @param string                               $outputChannelService
     * @param string                               $action
     */
    public function registerOutputChannelModifierAction($service, string $outputChannelService, string $action)
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelService, $action);
        $this->registryStorage->store($service, OutputChannelModifierActionInterface::class, $namespace, null, null, true);
    }

    /**
     * @param OutputChannelModifierFilterInterface $service
     * @param string                               $outputChannelService
     * @param string                               $filter
     */
    public function registerOutputChannelModifierFilter($service, string $outputChannelService, string $filter)
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelService);
        $this->registryStorage->store($service, OutputChannelModifierFilterInterface::class, $namespace, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelService(string $identifier)
    {
        return $this->registryStorage->has('outputChannel', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelService(string $identifier)
    {
        return $this->registryStorage->get('outputChannel', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelRuntimeQueryProvider(string $identifier)
    {
        return $this->registryStorage->has('runtimeQueryProvider', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeQueryProvider(string $identifier)
    {
        return $this->registryStorage->get('runtimeQueryProvider', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier)
    {
        return $this->registryStorage->has('runtimeOptionsBuilder', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $identifier)
    {
        return $this->registryStorage->get('runtimeOptionsBuilder', $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action)
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelServiceName, $action);

        return $this->registryStorage->hasOneByNamespace($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action)
    {
        $namespace = sprintf('outputChannelModifierAction_%s_%s', $outputChannelServiceName, $action);

        return $this->registryStorage->getByNamespace($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter)
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelServiceName);

        return $this->registryStorage->has($namespace, $filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter)
    {
        $namespace = sprintf('outputChannelModifierFilter_%s', $outputChannelServiceName);

        return $this->registryStorage->get($namespace, $filter);
    }
}
