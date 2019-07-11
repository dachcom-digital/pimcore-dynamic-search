<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContextData implements ContextDataInterface
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
    private $rawContextOptions;

    /**
     * @var array
     */
    private $runtimeValues;

    /**
     * @var array
     */
    private $parsedContextOptions;

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
        $this->rawContextOptions = $options;
        $this->runtimeValues = $runtimeValues;
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
    public function getContextDispatchType()
    {
        return $this->dispatchType;
    }

    /**
     * {@inheritDoc}
     */
    public function updateRuntimeValue(string $key, $value)
    {
        $this->runtimeValues[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getRuntimeValues()
    {
        return $this->runtimeValues;
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
    public function getResourceNormalizerName()
    {
        return $this->rawContextOptions['data_provider']['normalizer']['service'];
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
    public function getResourceNormalizerOptions(ResourceNormalizerInterface $resourceNormalizer)
    {
        $optionsResolver = new OptionsResolver();
        $resourceNormalizer->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions['data_provider']['normalizer']['options'];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $options = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException('resource_normalizer_options', $e->getMessage());
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelDocumentNormalizerOptions(DocumentNormalizerInterface $documentNormalizer, string $outputChannelName)
    {
        $optionsResolver = new OptionsResolver();
        $documentNormalizer->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions['output_channels'][$outputChannelName]['normalizer']['options'];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $options = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException('document_normalizer_options', $e->getMessage());
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelNormalizerName(string $outputChannelName)
    {
        if (!isset($this->rawContextOptions['output_channels'][$outputChannelName]['normalizer']['service'])) {
            return null;
        }

        return $this->rawContextOptions['output_channels'][$outputChannelName]['normalizer']['service'];
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
            $options = $optionsResolver->resolve($rawOptions);
            // append paginator options
            $options['paginator'] = $this->rawContextOptions['output_channels'][$outputChannelName]['paginator'];

            $this->parsedContextOptions[$outputChannelName] = $options;
        } catch (\Throwable $e) {
            throw new ContextConfigurationException($outputChannelName, $e->getMessage());
        }

        return $this->parsedContextOptions[$outputChannelName];
    }
}
