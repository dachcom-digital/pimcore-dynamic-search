<?php

namespace DynamicSearchBundle\Context;

class ContextDefinition implements ContextDefinitionInterface
{
    /**
     * @var string
     */
    private $dispatchType;

    /**
     * @var string
     */
    private $contextName;

    /**
     * @var array
     */
    private $contextOptions;

    /**
     * @var array
     */
    private $runtimeValues;

    /**
     * @param string $dispatchType
     * @param string $contextName
     * @param array  $options
     * @param array  $runtimeValues
     */
    public function __construct(string $dispatchType, string $contextName, array $options, array $runtimeValues = [])
    {
        $this->dispatchType = $dispatchType;
        $this->contextName = $contextName;
        $this->contextOptions = $options;
        $this->runtimeValues = $runtimeValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextDispatchType()
    {
        return $this->dispatchType;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRuntimeValue(string $key, $value)
    {
        $this->runtimeValues[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntimeValues()
    {
        return $this->runtimeValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataProviderOptions(string $providerBehaviour)
    {
        $alwaysOptions = [];
        if (isset($this->contextOptions['data_provider']['options']['always'])) {
            $alwaysOptions = $this->contextOptions['data_provider']['options']['always'];
        }

        $behaviourOptions = [];
        if (isset($this->contextOptions['data_provider']['options'][$providerBehaviour])) {
            $behaviourOptions = $this->contextOptions['data_provider']['options'][$providerBehaviour];
        }

        return array_merge($alwaysOptions, $behaviourOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexProviderOptions()
    {
        $options = [];
        if (isset($this->contextOptions['index_provider']['options'])) {
            $options = $this->contextOptions['index_provider']['options'];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceNormalizerOptions()
    {
        $options = [];
        if (isset($this->contextOptions['data_provider']['normalizer']['options'])) {
            $options = $this->contextOptions['data_provider']['normalizer']['options'];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelDocumentNormalizerOptions(string $outputChannelName)
    {
        $options = [];
        if (isset($this->contextOptions['output_channels'][$outputChannelName]['normalizer']['options'])) {
            $options = $this->contextOptions['output_channels'][$outputChannelName]['normalizer']['options'];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelOptions(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return [];
        }

        $options = [];
        if (isset($this->contextOptions['output_channels'][$outputChannelName]['options'])) {
            $options = $this->contextOptions['output_channels'][$outputChannelName]['options'];
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelPaginatorOptions(string $outputChannelName)
    {
        return $this->contextOptions['output_channels'][$outputChannelName]['paginator'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataProviderName()
    {
        return $this->contextOptions['data_provider']['service'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexProviderName()
    {
        return $this->contextOptions['index_provider']['service'];
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceNormalizerName()
    {
        return $this->contextOptions['data_provider']['normalizer']['service'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelNormalizerName(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName]['normalizer']['service'])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['normalizer']['service'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelServiceName(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['service'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelEnvironment(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return [];
        }

        $config = $this->contextOptions['output_channels'][$outputChannelName];

        return [
            'internal'                => $config['internal'],
            'multiple'                => $config['multiple'],
            'use_frontend_controller' => $config['use_frontend_controller'],
            'blocks'                  => isset($config['blocks']) ? $config['blocks'] : [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeQueryProvider(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['runtime_query_provider'];
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $outputChannelName)
    {
        if (!isset($this->contextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->contextOptions['output_channels'][$outputChannelName]['runtime_options_builder'];
    }
}
