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
    protected $definitionOptions;

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
     * @param array  $definitionOptions
     */
    public function __construct(string $dataNormalizerIdentifier, array $definitionOptions = [])
    {
        $this->currentLevel = 'root';
        $this->dataNormalizerIdentifier = $dataNormalizerIdentifier;

        $resolver = new OptionsResolver();
        $resolver->setDefaults(['allowPreProcessFieldDefinitions' => true]);
        $resolver->setRequired(['allowPreProcessFieldDefinitions']);
        $resolver->setAllowedTypes('allowPreProcessFieldDefinitions', ['bool']);

        try {
            $this->definitionOptions = $resolver->resolve($definitionOptions);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid document definition options: %s', $e->getMessage()));
        }
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

        try {
            $options = $resolver->resolve($definition);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Error while resolve document option field definition: %s', $e->getMessage()));
        }

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

        try {
            $options = $resolver->resolve($definition);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Error while resolve document simple field definition: %s', $e->getMessage()));
        }

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
        if ($this->definitionOptions['allowPreProcessFieldDefinitions'] === false) {
            throw new \Exception('Pre process field definitions are not allowed in current context (Maybe you are using a pre configured index provider)');
        }

        $resolver = new OptionsResolver();
        $resolver->setRequired(['type', 'configuration']);
        $resolver->setAllowedTypes('type', ['string']);
        $resolver->setAllowedTypes('configuration', ['array']);
        $resolver->setDefault('configuration', []);

        try {
            $dataTransformerOptions = $resolver->resolve($definition);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Error while resolve document pre process field definition: %s', $e->getMessage()));
        }

        $level = $this->levelCount + 1;
        $this->levelCount = $level;

        $options = [];
        $options['_field_type'] = 'pre_process_definition';
        $options['data_transformer'] = $dataTransformerOptions;
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
