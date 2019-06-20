<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContextData implements ContextDataInterface
{
    /**
     * @var string
     */
    private $contextName;

    /**
     * @var array
     */
    private $rawContextOptions;

    /**
     * @var array
     */
    private $parsedContextOptions;

    /**
     * @param string $contextName
     * @param array  $options
     */
    public function __construct(string $contextName, array $options)
    {
        $this->contextName = $contextName;
        $this->rawContextOptions = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProviderName()
    {
        return $this->rawContextOptions['data_provider']['service'];
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProviderName()
    {
        return $this->rawContextOptions['index_provider']['service'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProviderOptions(DataProviderInterface $dataProvider)
    {
        if (isset($this->parsedContextOptions['data_provider_options'])) {
            return $this->parsedContextOptions['data_provider_options'];
        }

        $optionsResolver = new OptionsResolver();
        $dataProvider->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions['data_provider']['options'];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $this->parsedContextOptions['data_provider_options'] = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException('data_provider_options', $e->getMessage());
        }

        return $this->parsedContextOptions['data_provider_options'];
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProviderOptions(IndexProviderInterface $indexProvider)
    {
        if (isset($this->parsedContextOptions['index_provider_options'])) {
            return $this->parsedContextOptions['index_provider_options'];
        }

        $optionsResolver = new OptionsResolver();
        $indexProvider->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions['index_provider']['options'];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $this->parsedContextOptions['index_provider_options'] = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException('index_provider_options', $e->getMessage());
        }

        return $this->parsedContextOptions['index_provider_options'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelServiceName(string $outputChannelName)
    {
        if (!isset($this->rawContextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->rawContextOptions['output_channels'][$outputChannelName]['service'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelRuntimeOptionsProvider(string $outputChannelName)
    {
        if (!isset($this->rawContextOptions['output_channels'][$outputChannelName])) {
            return null;
        }

        return $this->rawContextOptions['output_channels'][$outputChannelName]['runtime_options_provider'];
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelOptions(string $outputChannelName, OutputChannelInterface $outputChannel, ?OptionsResolver $optionsResolver = null)
    {
        if (!isset($this->rawContextOptions['output_channels'][$outputChannelName])) {
            return [];
        }

        if (isset($this->parsedContextOptions[$outputChannelName])) {
            return $this->parsedContextOptions[$outputChannelName];
        }

        $optionsResolver = $optionsResolver instanceof OptionsResolver ? $optionsResolver : new OptionsResolver();
        $outputChannel->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions['output_channels'][$outputChannelName]['options'];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $this->parsedContextOptions[$outputChannelName] = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException($outputChannelName, $e->getMessage());
        }

        return $this->parsedContextOptions[$outputChannelName];
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentOptionsConfig()
    {
        return $this->rawContextOptions['data_transformer']['document'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentFieldsConfig()
    {
        return $this->rawContextOptions['data_transformer']['fields'];
    }
}
