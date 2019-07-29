<?php

namespace DynamicSearchBundle\Document\Definition;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PreProcessedDocumentDefinition implements PreProcessedDocumentDefinitionInterface
{
    /**
     * @var array
     */
    protected $indexFieldDefinitions;

    /**
     * {@inheritdoc}
     */
    public function addSimpleDocumentFieldDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['name', 'index_transformer', 'value']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('index_transformer', ['array']);

        $options = $resolver->resolve($definition);

        if (!isset($options['index_transformer']['type'])) {
            $options['index_transformer']['type'] = null;
        }

        if (!isset($options['index_transformer']['configuration'])) {
            $options['index_transformer']['configuration'] = [];
        }

        $options['_field_type'] = 'simple_processed_definition';

        $this->indexFieldDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentFieldDefinitions(): array
    {
        return !is_array($this->indexFieldDefinitions) ? [] : $this->indexFieldDefinitions;
    }
}
