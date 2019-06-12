<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\UnresolvedContextConfigurationException;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;
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
    public function getDataProvider()
    {
        return $this->rawContextOptions['data_provider'];
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProvider()
    {
        return $this->rawContextOptions['index_provider'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDataProviderOptions()
    {
        if (!isset($this->parsedContextOptions[self::DATA_PROVIDER_OPTIONS])) {
            throw new UnresolvedContextConfigurationException(self::DATA_PROVIDER_OPTIONS);
        }

        $options = $this->parsedContextOptions[self::DATA_PROVIDER_OPTIONS];

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexProviderOptions()
    {
        if (!isset($this->parsedContextOptions[self::INDEX_PROVIDER_OPTIONS])) {
            throw new UnresolvedContextConfigurationException(self::INDEX_PROVIDER_OPTIONS);
        }

        $options = $this->parsedContextOptions[self::INDEX_PROVIDER_OPTIONS];

        return $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataTransformOptions($transformerName)
    {
        if (!isset($this->parsedContextOptions[self::DATA_TRANSFORMER_OPTIONS])) {
            throw new UnresolvedContextConfigurationException($transformerName);
        }

        $options = $this->parsedContextOptions[self::DATA_TRANSFORMER_OPTIONS];

        return isset($options[$transformerName]) && is_array($options[$transformerName]) ? $options[$transformerName] : [];
    }

    /**
     * {@inheritDoc}
     */
    public function assertValidContextProviderOptions(OptionAwareResolverInterface $resolver, string $providerType)
    {
        $optionsResolver = new OptionsResolver();
        $resolver->configureOptions($optionsResolver);

        $rawOptions = $this->rawContextOptions[$providerType];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $this->parsedContextOptions[$providerType] = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException($providerType, $e->getMessage());
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function assertValidContextTransformerOptions(OptionAwareResolverInterface $resolver, string $transformerName)
    {
        $optionsResolver = new OptionsResolver();
        $resolver->configureOptions($optionsResolver);

        if (!is_array($this->parsedContextOptions[self::DATA_TRANSFORMER_OPTIONS])) {
            $this->parsedContextOptions[self::DATA_TRANSFORMER_OPTIONS] = [];
        }

        $rawOptions = $this->rawContextOptions[self::DATA_TRANSFORMER_OPTIONS][$transformerName];
        if (!is_array($rawOptions)) {
            $rawOptions = [];
        }

        try {
            $this->parsedContextOptions[self::DATA_TRANSFORMER_OPTIONS][$transformerName] = $optionsResolver->resolve($rawOptions);
        } catch (\Throwable $e) {
            throw new ContextConfigurationException($transformerName, $e->getMessage());
        }

        return $this;
    }
}
