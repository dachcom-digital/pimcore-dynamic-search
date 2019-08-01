<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

class OutputChannelRegistry implements OutputChannelRegistryInterface
{
    /**
     * @var array
     */
    protected $outputChannelServices;

    /**
     * @var array
     */
    protected $runtimeOptionsBuilder;

    /**
     * @var array
     */
    protected $runtimeQueryProvider;

    /**
     * @var array
     */
    protected $outputChannelModifierAction;

    /**
     * @var array
     */
    protected $outputChannelModifierFilter;

    /**
     * @param OutputChannelInterface $service
     * @param string                 $identifier
     */
    public function registerOutputChannelService($service, string $identifier)
    {
        if (!in_array(OutputChannelInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    OutputChannelInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->outputChannelServices[$identifier] = $service;
    }

    /**
     * @param RuntimeQueryProviderInterface $service
     * @param string                        $identifier
     */
    public function registerOutputChannelRuntimeQueryProvider($service, string $identifier)
    {
        if (!in_array(RuntimeQueryProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    RuntimeQueryProviderInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->runtimeQueryProvider[$identifier] = $service;
    }

    /**
     * @param RuntimeOptionsBuilderInterface $service
     * @param string                          $identifier
     */
    public function registerOutputChannelRuntimeOptionsBuilder($service, string $identifier)
    {
        if (!in_array(RuntimeOptionsBuilderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    RuntimeOptionsBuilderInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->runtimeOptionsBuilder[$identifier] = $service;
    }

    /**
     * @param OutputChannelModifierActionInterface $service
     * @param string                               $outputChannelService
     * @param string                               $action
     */
    public function registerOutputChannelModifierAction($service, string $outputChannelService, string $action)
    {
        if (!in_array(OutputChannelModifierActionInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    OutputChannelModifierActionInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        if (!isset($this->outputChannelModifierAction[$outputChannelService])) {
            $this->outputChannelModifierAction[$outputChannelService] = [];
        }

        if (!isset($this->outputChannelModifierAction[$outputChannelService][$action])) {
            $this->outputChannelModifierAction[$outputChannelService][$action] = [];
        }

        $this->outputChannelModifierAction[$outputChannelService][$action][] = $service;
    }

    /**
     * @param OutputChannelModifierFilterInterface $service
     * @param string                               $outputChannelService
     * @param string                               $filter
     */
    public function registerOutputChannelModifierFilter($service, string $outputChannelService, string $filter)
    {
        if (!in_array(OutputChannelModifierFilterInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    OutputChannelModifierFilterInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        if (!isset($this->outputChannelModifierFilter[$outputChannelService])) {
            $this->outputChannelModifierFilter[$outputChannelService] = [];
        }

        $this->outputChannelModifierFilter[$outputChannelService][$filter] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelService(string $identifier)
    {
        return isset($this->outputChannelServices[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelService(string $identifier)
    {
        return $this->outputChannelServices[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelRuntimeQueryProvider(string $identifier)
    {
        return isset($this->runtimeQueryProvider[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeQueryProvider(string $identifier)
    {
        return $this->runtimeQueryProvider[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelRuntimeOptionsBuilder(string $identifier)
    {
        return isset($this->runtimeOptionsBuilder[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $identifier)
    {
        return $this->runtimeOptionsBuilder[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierAction(string $outputChannelServiceName, string $action)
    {
        return isset($this->outputChannelModifierAction[$outputChannelServiceName][$action]) &&
            is_array($this->outputChannelModifierAction[$outputChannelServiceName][$action]) &&
            count($this->outputChannelModifierAction[$outputChannelServiceName][$action]) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action)
    {
        return $this->outputChannelModifierAction[$outputChannelServiceName][$action];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierFilter(string $outputChannelServiceName, string $filter)
    {
        return isset($this->outputChannelModifierFilter[$outputChannelServiceName][$filter]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter)
    {
        return $this->outputChannelModifierFilter[$outputChannelServiceName][$filter];
    }
}
