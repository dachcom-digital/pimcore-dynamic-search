<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;

class OutputChannelRegistry implements OutputChannelRegistryInterface
{
    /**
     * @var array
     */
    protected $outputChannel;

    /**
     * @var array
     */
    protected $runtimeOptionsProvider;

    /**
     * @var array
     */
    protected $outputChannelModifierAction;

    /**
     * @var array
     */
    protected $outputChannelModifierFilter;

    /**
     * @param        $service
     * @param string $type
     * @param string $identifier
     */
    public function registerOutputChannel($service, string $type, string $identifier)
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

        if (!isset($this->outputChannel[$type])) {
            $this->outputChannel[$type] = [];
        }

        $this->outputChannel[$type][$identifier] = $service;
    }

    /**
     * @param        $service
     * @param string $identifier
     */
    public function registerOutputChannelRuntimeOptionsProvider($service, string $identifier)
    {
        if (!in_array(RuntimeOptionsProviderInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s needs to implement "%s", "%s" given.',
                    get_class($service),
                    RuntimeOptionsProviderInterface::class,
                    implode(', ', class_implements($service))
                )
            );
        }

        $this->runtimeOptionsProvider[$identifier] = $service;
    }

    /**
     * @param        $service
     * @param string $outputProvider
     * @param string $outputChannel
     * @param string $action
     */
    public function registerOutputChannelModifierAction($service, string $outputProvider, string $outputChannel, string $action)
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

        if (!isset($this->outputChannelModifierAction[$outputProvider])) {
            $this->outputChannelModifierAction[$outputProvider] = [];
        }

        if (!isset($this->outputChannelModifierAction[$outputProvider][$outputChannel])) {
            $this->outputChannelModifierAction[$outputProvider][$outputChannel] = [];
        }

        if (!isset($this->outputChannelModifierAction[$outputProvider][$outputChannel][$action])) {
            $this->outputChannelModifierAction[$outputProvider][$outputChannel][$action] = [];
        }

        $this->outputChannelModifierAction[$outputProvider][$outputChannel][$action][] = $service;
    }

    /**
     * @param        $service
     * @param string $outputProvider
     * @param string $outputChannel
     * @param string $filter
     */
    public function registerOutputChannelModifierFilter($service, string $outputProvider, string $outputChannel, string $filter)
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

        if (!isset($this->outputChannelModifierFilter[$outputProvider])) {
            $this->outputChannelModifierFilter[$outputProvider] = [];
        }

        if (!isset($this->outputChannelModifierFilter[$outputProvider][$outputChannel])) {
            $this->outputChannelModifierFilter[$outputProvider][$outputChannel] = [];
        }

        $this->outputChannelModifierFilter[$outputProvider][$filter][$outputChannel] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannel(string $type, string $identifier)
    {
        return isset($this->outputChannel[$type][$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannel(string $type, string $identifier)
    {
        return $this->outputChannel[$type][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelRuntimeOptionsProvider(string $identifier)
    {
        return isset($this->runtimeOptionsProvider[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeOptionsProvider(string $identifier)
    {
        return $this->runtimeOptionsProvider[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierAction(string $outputProvider, string $outputChannel, string $action)
    {
        return isset($this->outputChannelModifierAction[$outputProvider][$outputChannel]) &&
            isset($this->outputChannelModifierAction[$outputProvider][$outputChannel][$action]) &&
            is_array($this->outputChannelModifierAction[$outputProvider][$outputChannel][$action]) &&
            count($this->outputChannelModifierAction[$outputProvider][$outputChannel][$action]) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierAction(string $outputProvider, string $outputChannel, string $action)
    {
        return $this->outputChannelModifierAction[$outputProvider][$outputChannel][$action];
    }

    /**
     * {@inheritdoc}
     */
    public function hasOutputChannelModifierFilter(string $outputProvider, string $outputChannel, string $filter)
    {
        return isset($this->outputChannelModifierFilter[$outputProvider][$filter][$outputChannel]);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelModifierFilter(string $outputProvider, string $outputChannel, string $filter)
    {
        return $this->outputChannelModifierFilter[$outputProvider][$filter][$outputChannel];
    }
}
