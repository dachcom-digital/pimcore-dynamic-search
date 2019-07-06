<?php

namespace DynamicSearchBundle\Document;

use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexDocumentDefinition implements IndexDocumentDefinitionInterface
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
    public function getOptionFieldDefinition(): array
    {
        return !is_array($this->optionFieldDefinitions) ? [] : $this->optionFieldDefinitions;
    }

    /**
     * {@inheritDoc}
     */
    public function addIndexFieldDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['name', 'index_transformer', 'data_transformer']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('index_transformer', ['array']);
        $resolver->setAllowedTypes('data_transformer', ['array']);

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

        $this->indexFieldDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getIndexFieldDefinition(): array
    {
        return !is_array($this->indexFieldDefinitions) ? [] : $this->indexFieldDefinitions;
    }

}