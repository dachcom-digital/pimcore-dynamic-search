<?php

namespace DynamicSearchBundle\Generator;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Resource\Container\IndexFieldContainer;
use DynamicSearchBundle\Resource\Container\OptionFieldContainer;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\FieldTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexDocumentGenerator implements IndexDocumentGeneratorInterface
{
    /**
     * @var ContextDefinitionBuilderInterface
     */
    protected $contextDefinitionBuilder;

    /**
     * @var TransformerManagerInterface
     */
    protected $transformerManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var DocumentDefinitionManagerInterface
     */
    protected $documentDefinitionManager;

    /**
     * @param ContextDefinitionBuilderInterface  $contextDefinitionBuilder
     * @param TransformerManagerInterface        $transformerManager
     * @param IndexManagerInterface              $indexManager
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     */
    public function __construct(
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager,
        ResourceHarmonizerInterface $resourceHarmonizer,
        DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->documentDefinitionManager = $documentDefinitionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        ResourceContainerInterface $resourceContainer,
        array $options = []
    ) {
        $generatorOptions = $this->buildOptions($options);
        $documentDefinition = $this->generateDocumentDefinition($contextDefinition, $resourceMeta, $generatorOptions);
        $indexDocument = $this->generateIndexDocument($resourceMeta, $documentDefinition);

        return $this->populateIndexDocument($contextDefinition, $indexDocument, $resourceContainer, $documentDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public function generateWithoutData(
        ContextDefinitionInterface $contextDefinition,
        array $options = []
    ) {

        $generatorOptions = $this->buildOptions($options);
        $documentDefinition = $this->generateDocumentDefinition($contextDefinition, null, $generatorOptions);
        $indexDocument = $this->generateIndexDocument(null, $documentDefinition);

        foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {

            $fieldType = $fieldDefinitionOptions['_field_type'];
            if ($fieldType !== 'simple_definition') {
                continue;
            }

            $this->processDocumentIndexTransformerField($contextDefinition, $indexDocument, $fieldDefinitionOptions, null);
        }

        return $indexDocument;
    }

    protected function buildOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['preConfiguredIndexProvider']);
        $resolver->setAllowedTypes('preConfiguredIndexProvider', ['bool']);
        $resolver->setDefaults([
            'preConfiguredIndexProvider' => false,
        ]);

        return $resolver->resolve($options);
    }

    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param IndexDocument               $indexDocument
     * @param ResourceContainerInterface  $resourceContainer
     * @param DocumentDefinitionInterface $documentDefinition
     *
     * @return IndexDocument
     * @throws \Exception
     */
    protected function populateIndexDocument(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        ResourceContainerInterface $resourceContainer,
        DocumentDefinitionInterface $documentDefinition
    ) {

        $resourceScaffolderName = $resourceContainer->getResourceScaffolderIdentifier();

        foreach ($documentDefinition->getOptionFieldDefinitions() as $documentDefinitionOptions) {
            $fieldName = $documentDefinitionOptions['name'];
            $dataTransformerOptions = $documentDefinitionOptions['data_transformer'];
            $transformedData = $this->dispatchResourceFieldTransformer($dataTransformerOptions, $resourceScaffolderName, $resourceContainer);

            if ($transformedData === null) {
                // no error: transformer is allowed to refuse data
                continue;
            }

            $optionFieldContainer = new OptionFieldContainer($fieldName, $transformedData);
            $indexDocument->addOptionField($optionFieldContainer);
        }

        foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {
            $this->processDocumentDataTransformerField(
                $contextDefinition,
                $indexDocument,
                $resourceContainer,
                $documentDefinition,
                $fieldDefinitionOptions,
                $resourceScaffolderName
            );
        }

        return $indexDocument;
    }

    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param IndexDocument               $indexDocument
     * @param ResourceContainerInterface  $resourceContainer
     * @param DocumentDefinitionInterface $documentDefinition
     * @param array                       $fieldDefinitionOptions
     * @param string                      $resourceScaffolderName
     *
     * @throws \Exception
     */
    protected function processDocumentDataTransformerField(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        ResourceContainerInterface $resourceContainer,
        DocumentDefinitionInterface $documentDefinition,
        array $fieldDefinitionOptions,
        string $resourceScaffolderName
    ) {
        $fieldType = $fieldDefinitionOptions['_field_type'];
        $dataTransformerOptions = $fieldDefinitionOptions['data_transformer'];

        $transformedData = $this->dispatchResourceFieldTransformer($dataTransformerOptions, $resourceScaffolderName, $resourceContainer);

        if ($transformedData === null) {
            // no error: transformer is allowed to refuse data
            return;
        }

        if ($fieldType === 'pre_process_definition') {

            $documentDefinition->setCurrentLevel($fieldDefinitionOptions['level']);

            call_user_func($fieldDefinitionOptions['closure'], $documentDefinition, $transformedData);

            foreach ($documentDefinition->getDocumentFieldDefinitions() as $fieldDefinitionOptions) {
                $this->processDocumentDataTransformerField(
                    $contextDefinition,
                    $indexDocument,
                    $resourceContainer,
                    $documentDefinition,
                    $fieldDefinitionOptions,
                    $resourceScaffolderName
                );
            }
        } elseif ($fieldType === 'simple_definition') {
            $this->processDocumentIndexTransformerField($contextDefinition, $indexDocument, $fieldDefinitionOptions, $transformedData);
        } else {
            throw new \Exception(sprintf('Index field type %s not found.', $fieldType));
        }
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param IndexDocument              $indexDocument
     * @param array                      $fieldDefinitionOptions
     * @param mixed                      $transformedData
     */
    protected function processDocumentIndexTransformerField(
        ContextDefinitionInterface $contextDefinition,
        IndexDocument $indexDocument,
        array $fieldDefinitionOptions,
        $transformedData
    ) {
        $fieldName = $fieldDefinitionOptions['name'];
        $indexTransformerOptions = $fieldDefinitionOptions['index_transformer'];

        $transformedIndexData = $this->dispatchIndexTransformer($contextDefinition, $fieldName, $indexTransformerOptions, $transformedData);
        if ($transformedIndexData === null) {
            // no error?
            return;
        }

        $indexFieldContainer = new IndexFieldContainer($fieldName, $indexTransformerOptions['type'], $transformedIndexData);
        $indexDocument->addIndexField($indexFieldContainer);
    }

    /**
     * @param array                      $options
     * @param string                     $dispatchTransformerName
     * @param ResourceContainerInterface $resourceContainer
     *
     * @return mixed|null
     *
     * @throws \Exception
     */
    protected function dispatchResourceFieldTransformer(array $options, string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        $fieldTransformerName = $options['type'];
        $fieldTransformerConfiguration = $options['configuration'];

        $fieldTransformer = $this->transformerManager->getResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName, $fieldTransformerConfiguration);

        if (!$fieldTransformer instanceof FieldTransformerInterface) {
            return null;
        }

        try {
            $transformedData = $fieldTransformer->transformData($dispatchTransformerName, $resourceContainer);
        } catch (\Throwable $e) {
            throw new \Exception(
                sprintf('Error while transform field resource with service "%s": %s', $fieldTransformerName, $e->getMessage()));
        }

        if ($transformedData === null) {
            return null;
        }

        if ($transformedData === '') {
            return null;
        }

        return $transformedData;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $indexFieldName
     * @param array                      $options
     * @param mixed                      $transformedData
     *
     * @return mixed|null
     *
     * @throws \Exception
     */
    protected function dispatchIndexTransformer(ContextDefinitionInterface $contextDefinition, string $indexFieldName, array $options, $transformedData)
    {
        $indexTypeName = $options['type'];
        $indexTypeConfiguration = $options['configuration'];

        $indexFieldBuilder = $this->indexManager->getIndexField($contextDefinition, $indexTypeName);
        if (!$indexFieldBuilder instanceof IndexFieldInterface) {
            return null;
        }

        try {
            $indexFieldData = $indexFieldBuilder->build($indexFieldName, $transformedData, $indexTypeConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(
                sprintf('Error while transform field index with service "%s": %s', $indexTypeName, $e->getMessage()));
        }

        if ($indexFieldData === null) {
            return null;
        }

        return $indexFieldData;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface|null $resourceMeta
     * @param array                      $generatorOptions
     *
     * @return DocumentDefinitionInterface
     *
     * @throws \Exception
     * @throws SilentException
     */
    protected function generateDocumentDefinition(ContextDefinitionInterface $contextDefinition, ?ResourceMetaInterface $resourceMeta, array $generatorOptions)
    {
        $options = [
            'allowPreProcessFieldDefinitions' => $generatorOptions['preConfiguredIndexProvider'] === false
        ];

        if ($resourceMeta === null) {
            $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinitionForContext($contextDefinition, $options);
        } else {
            $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinition($contextDefinition, $resourceMeta, $options);
        }

        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            throw new \Exception(sprintf('No document definition generated. Probably no applicable document definition builder was found. Skipping...'));
        }

        if (count($documentDefinition->getDocumentFieldDefinitions()) === 0) {
            throw new SilentException(sprintf('Document Definition does not have any defined field. Skipping...'));
        }

        return $documentDefinition;
    }

    /**
     * @param ResourceMetaInterface|null  $resourceMeta
     * @param DocumentDefinitionInterface $documentDefinition
     *
     * @return IndexDocument
     *
     * @throws \Exception
     */
    protected function generateIndexDocument(?ResourceMetaInterface $resourceMeta, DocumentDefinitionInterface $documentDefinition)
    {
        $indexDocument = new IndexDocument($resourceMeta, $documentDefinition->getDocumentConfiguration());

        if (!$indexDocument instanceof IndexDocument) {
            throw new \Exception(sprintf('Index Document needs to be instance of %s. Skipping...', IndexDocument::class));
        }

        return $indexDocument;
    }
}
