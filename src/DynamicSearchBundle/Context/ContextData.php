<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\UnresolvedContextConfigurationException;
use DynamicSearchBundle\Provider\IndexProviderInterface;
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
    public function getDocumentOptionsConfig()
    {
        return $this->rawContextOptions['data_transformer_options']['document_options'];
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentFieldsConfig()
    {
        return $this->rawContextOptions['data_transformer_options']['document_fields'];
    }

    /**
     * {@inheritDoc}
     */
    public function assertValidContextProviderOptions(OptionAwareResolverInterface $resolver, string $providerType)
    {
        $optionsResolver = $this->getDefaultOptionsResolver($resolver);

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

    protected function getDefaultOptionsResolver(OptionAwareResolverInterface $resolver)
    {
        $optionsResolver = new OptionsResolver();
        $resolver->configureOptions($optionsResolver);

        if ($resolver instanceof IndexProviderInterface) {
            $optionsResolver->setRequired(['output_channel_autocomplete', 'output_channel_search']);
            $optionsResolver->setAllowedTypes('output_channel_autocomplete', ['string']);
            $optionsResolver->setAllowedTypes('output_channel_search', ['string']);
        }

        return $optionsResolver;
    }
}
