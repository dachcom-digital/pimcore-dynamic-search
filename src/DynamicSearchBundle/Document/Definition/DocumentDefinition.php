<?php

namespace DynamicSearchBundle\Document\Definition;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentDefinition implements DocumentDefinitionInterface
{
    /**
     * @var string|int
     */
    protected $currentLevel;

    /**
     * @var int
     */
    protected $levelCount = 0;

    /**
     * @var string
     */
    protected $dataNormalizerIdentifier;

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
     * @param string $dataNormalizerIdentifier
     */
    public function __construct(string $dataNormalizerIdentifier)
    {
        $this->currentLevel = 'root';
        $this->dataNormalizerIdentifier = $dataNormalizerIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataNormalizerIdentifier()
    {
        return $this->dataNormalizerIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLevel($currentLevel)
    {
        $this->currentLevel = $currentLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function setDocumentConfiguration(array $documentConfiguration)
    {
        $this->documentConfiguration = $documentConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentConfiguration()
    {
        return empty($this->documentConfiguration) ? [] : $this->documentConfiguration;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getOptionFieldDefinitions(): array
    {
        return !is_array($this->optionFieldDefinitions) ? [] : $this->optionFieldDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function addSimpleDocumentFieldDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['name', 'index_transformer', 'data_transformer']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('index_transformer', ['array']);
        $resolver->setAllowedTypes('data_transformer', ['array']);

        $options = $resolver->resolve($definition);
        $options['level'] = 'root';
        $options['_field_type'] = 'simple_definition';

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

        $this->indexFieldDefinitions[$this->currentLevel][] = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPreProcessFieldDefinition(array $definition, \Closure $closure)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['type', 'configuration']);
        $resolver->setAllowedTypes('type', ['string']);
        $resolver->setAllowedTypes('configuration', ['array']);
        $resolver->setDefault('configuration', []);

        $level = $this->levelCount + 1;
        $this->levelCount = $level;

        $options = [];
        $options['_field_type'] = 'pre_process_definition';
        $options['data_transformer'] = $resolver->resolve($definition);
        $options['closure'] = $closure;
        $options['level'] = $level;

        $this->indexFieldDefinitions[$this->currentLevel][] = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentFieldDefinitions(): array
    {
        return !isset($this->indexFieldDefinitions[$this->currentLevel]) || !is_array($this->indexFieldDefinitions[$this->currentLevel])
            ? []
            : $this->indexFieldDefinitions[$this->currentLevel];
    }
}
