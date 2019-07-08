<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentDefinition implements DocumentDefinitionInterface
{
    /**
     * @var array
     */
    protected $documentConfiguration;

    /**
     * @var array
     */
    protected $optionFieldDefinitions;

    /**
     * @var array
     */
    protected $indexFieldDefinitions;

    /**
     * @var ResourceMetaInterface
     */
    public $resourceMeta;

    /**
     * @var array
     */
    public $options;

    /**
     * @param ResourceMetaInterface $resourceMeta
     * @param array                 $options
     */
    public function __construct(ResourceMetaInterface $resourceMeta, array $options = [])
    {
        $this->resourceMeta = $resourceMeta;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceMeta()
    {
        return $this->resourceMeta;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritDoc}
     */
    public function setDocumentConfiguration(array $documentConfiguration)
    {
        $this->documentConfiguration = $documentConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentConfiguration()
    {
        return empty($this->documentConfiguration) ? [] : $this->documentConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function addOptionFieldDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['name', 'data_transformer']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('data_transformer', ['array']);

        $options = $resolver->resolve($definition);

        if (!isset($options['data_transformer']['type'])) {
            $options['data_transformer']['type'] = null;
        }

        if (!isset($options['data_transformer']['configuration'])) {
            $options['data_transformer']['configuration'] = [];
        }

        $this->optionFieldDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionFieldDefinitions(): array
    {
        return !is_array($this->optionFieldDefinitions) ? [] : $this->optionFieldDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function addDocumentFieldDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['name', 'index_transformer', 'data_transformer', 'normalizer']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('index_transformer', ['array']);
        $resolver->setAllowedTypes('data_transformer', ['array']);
        $resolver->setAllowedTypes('normalizer', ['array']);

        $resolver->setDefault('normalizer', []);

        $options = $resolver->resolve($definition);

        if (!isset($options['index_transformer']['type'])) {
            $options['index_transformer']['type'] = null;
        }

        if (!isset($options['index_transformer']['configuration'])) {
            $options['index_transformer']['configuration'] = [];
        }

        if (!isset($options['data_transformer']['type'])) {
            $options['data_transformer']['type'] = null;
        }

        if (!isset($options['data_transformer']['configuration'])) {
            $options['data_transformer']['configuration'] = [];
        }

        $channelVisibility = [];
        foreach (ContextDataInterface::AVAILABLE_OUTPUT_CHANNEL_TYPES as $channel) {
            $channelVisibility[$channel] = true;
        }

        if (!isset($options['normalizer']['channel_visibility'])) {
            $options['normalizer']['channel_visibility'] = $channelVisibility;
        }

        $options['normalizer']['channel_visibility'] = array_merge($channelVisibility, $options['normalizer']['channel_visibility']);


        $this->indexFieldDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDocumentFieldDefinitions(): array
    {
        return !is_array($this->indexFieldDefinitions) ? [] : $this->indexFieldDefinitions;
    }

}