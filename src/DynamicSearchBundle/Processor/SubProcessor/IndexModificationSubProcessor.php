<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Document\Definition\IndexDocumentDefinition;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\Definition\IndexDocumentDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\ResourceNormalizerManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\Transformer\Container\OptionFieldContainer;
use DynamicSearchBundle\Transformer\Container\ResourceContainer;
use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;
use DynamicSearchBundle\Transformer\Container\IndexFieldContainer;
use DynamicSearchBundle\Transformer\DocumentTransformerContainerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;

class IndexModificationSubProcessor implements IndexModificationSubProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var TransformerManagerInterface
     */
    protected $transformerManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var ResourceNormalizerManagerInterface
     */
    protected $resourceNormalizerManager;

    /**
     * @var DocumentDefinitionManagerInterface
     */
    protected $documentDefinitionManager;

    /**
     * @var bool
     */
    protected $validProcessRunning;

    /**
     * @param LoggerInterface                    $logger
     * @param ConfigurationInterface             $configuration
     * @param TransformerManagerInterface        $transformerManager
     * @param IndexManagerInterface              $indexManager
     * @param ResourceNormalizerManagerInterface $resourceNormalizerManager
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager,
        ResourceNormalizerManagerInterface $resourceNormalizerManager,
        DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
        $this->resourceNormalizerManager = $resourceNormalizerManager;
        $this->documentDefinitionManager = $documentDefinitionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(ContextDataInterface $contextData, $resource)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextData);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Unable to load index provider "%s".', $contextData->getIndexProviderName()));
        }

        $documentTransformerContainer = $this->transformerManager->getDocumentTransformer($contextData, $resource);
        if (!$documentTransformerContainer instanceof DocumentTransformerContainerInterface) {
            $this->logger->error(
                'No document transformer has been found. Skipping...',
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        $transformedDocumentData = $documentTransformerContainer->getTransformer()->transformData($contextData, $resource);
        $transformedResourceContainer = new ResourceContainer($resource, $transformedDocumentData);

        $resourceNormalizer = $this->resourceNormalizerManager->getResourceNormalizer($contextData);
        if (!$resourceNormalizer instanceof ResourceNormalizerInterface) {
            $this->logger->error(
                'No resource normalizer found. Skipping...',
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        try {
            $normalizedResourceStack = $resourceNormalizer->normalizeToResourceStack($contextData, $transformedResourceContainer);
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf(
                    'Error while generating normalized resource stack with identifier "%s". Error was: %s. Skipping...',
                    $contextData->getResourceNormalizerName(),
                    $e->getMessage()
                ),
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        if (count($normalizedResourceStack) === 0) {
            $this->logger->error(
                sprintf('No normalized resources generated. Skipping...'),
                $contextData->getIndexProviderName(), $contextData->getName()
            );
            return;
        }

        foreach ($normalizedResourceStack as $normalizedResource) {

            if (!$normalizedResource instanceof NormalizedDataResourceInterface) {
                $this->logger->error(
                    sprintf('Normalized resource needs to be instance of %s. Skipping...', NormalizedDataResourceInterface::class),
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            if (empty($normalizedResource->getResourceId())) {
                $this->logger->error(
                    'Unable to generate index document: No resource id found. Skipping...',
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            $documentDefinitionBuilder = $this->documentDefinitionManager->getDocumentDefinitionBuilder($contextData);
            if (!$documentDefinitionBuilder instanceof DocumentDefinitionBuilderInterface) {
                $this->logger->error(
                    sprintf(
                        'No index document definition builder for identifier "%s" found. Skipping...',
                        $contextData->getDocumentDefinitionBuilderName()
                    ),
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            try {
                $definition = new IndexDocumentDefinition();
                $indexDocumentDefinition = $documentDefinitionBuilder->buildInputDefinition($definition, $normalizedResource);
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf(
                        'Error while building index document definition with "%s". Error was: %s. Skipping...',
                        $contextData->getDocumentDefinitionBuilderName(),
                        $e->getMessage()
                    ),
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            $indexDocument = $this->generateIndexDocument(
                $contextData,
                $normalizedResource,
                $indexDocumentDefinition,
                $documentTransformerContainer->getIdentifier()
            );

            if (!$indexDocument instanceof IndexDocument) {
                $this->logger->error(
                    sprintf('Index Document needs to be instance of %s. Skipping...', IndexDocument::class),
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            if (count($indexDocument->getIndexFields()) === 0) {
                $this->logger->error(
                    sprintf('Index Document does not have any index fields. Skip Indexing...'),
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            $this->logger->debug(
                sprintf('Index Document with %d fields successfully generated. Used "%s" transformer',
                    count($indexDocument->getIndexFields()),
                    $indexDocument->getDispatchedTransformerName()
                ), $contextData->getIndexProviderName(), $contextData->getName());

            try {
                $contextData->updateRuntimeValue('index_document', $indexDocument);
                $indexProvider->execute($contextData);
            } catch (\Throwable $e) {
                throw new RuntimeException(sprintf('Error while executing index modification. Error was: "%s".', $e->getMessage()));
            }
        }
    }

    /**
     * @param ContextDataInterface             $contextData
     * @param NormalizedDataResourceInterface  $normalizedDataResource
     * @param IndexDocumentDefinitionInterface $indexDocumentDefinition
     * @param string                           $dispatchTransformerName
     *
     * @return IndexDocument
     */
    protected function generateIndexDocument(
        ContextDataInterface $contextData,
        NormalizedDataResourceInterface $normalizedDataResource,
        IndexDocumentDefinitionInterface $indexDocumentDefinition,
        string $dispatchTransformerName
    ) {

        $indexDocument = new IndexDocument($normalizedDataResource->getResourceId(), $indexDocumentDefinition->getDocumentConfiguration(), $dispatchTransformerName);

        foreach ($indexDocumentDefinition->getOptionFieldDefinitions() as $documentDefinitionOptions) {

            $fieldName = $documentDefinitionOptions['name'];
            $dataTransformerOptions = $documentDefinitionOptions['data_transformer'];
            $transformedData = $this->dispatchDataTransformer($dataTransformerOptions, $dispatchTransformerName, $normalizedDataResource->getResourceContainer());

            if ($transformedData === null) {
                // error?
                continue;
            }

            $optionFieldContainer = new OptionFieldContainer($fieldName, $transformedData);
            $indexDocument->addOptionField($optionFieldContainer);
        }

        foreach ($indexDocumentDefinition->getIndexFieldDefinitions() as $fieldDefinitionOptions) {

            $fieldName = $fieldDefinitionOptions['name'];
            $dataTransformerOptions = $fieldDefinitionOptions['data_transformer'];
            $indexTransformerOptions = $fieldDefinitionOptions['index_transformer'];

            $transformedData = $this->dispatchDataTransformer($dataTransformerOptions, $dispatchTransformerName, $normalizedDataResource->getResourceContainer());
            if ($transformedData === null) {
                // error?
                continue;
            }

            $transformedIndexData = $this->dispatchIndexTransformer($contextData, $fieldName, $indexTransformerOptions, $transformedData);
            if ($transformedIndexData === null) {
                // error?
                continue;
            }

            $indexFieldContainer = new IndexFieldContainer($fieldName, $indexTransformerOptions['type'], $transformedIndexData);
            $indexDocument->addIndexField($indexFieldContainer);

        }

        return $indexDocument;
    }

    /**
     * @param array                      $options
     * @param string                     $dispatchTransformerName
     * @param ResourceContainerInterface $resourceContainer
     *
     * @return mixed|null
     */
    protected function dispatchDataTransformer(array $options, string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        $fieldTransformerName = $options['type'];
        $fieldTransformerConfiguration = $options['configuration'];

        $fieldTransformer = $this->transformerManager->getFieldTransformer($dispatchTransformerName, $fieldTransformerName, $fieldTransformerConfiguration);
        if (!$fieldTransformer instanceof FieldTransformerInterface) {
            return null;
        }

        $transformedData = $fieldTransformer->transformData($dispatchTransformerName, $resourceContainer);
        if ($transformedData === null) {
            return null;
        }

        return $transformedData;
    }

    /**
     * @param ContextDataInterface $contextData
     * @param string               $indexFieldName
     * @param array                $options
     * @param mixed                $transformedData
     *
     * @return mixed|null
     */
    protected function dispatchIndexTransformer(ContextDataInterface $contextData, string $indexFieldName, array $options, $transformedData)
    {
        $indexTypeName = $options['type'];
        $indexTypeConfiguration = $options['configuration'];

        $indexFieldBuilder = $this->indexManager->getIndexField($contextData, $indexTypeName);
        if (!$indexFieldBuilder instanceof IndexFieldInterface) {
            return null;
        }

        $indexFieldData = $indexFieldBuilder->build($indexFieldName, $transformedData, $indexTypeConfiguration);
        if ($indexFieldData === null) {
            return null;
        }

        return $indexFieldData;
    }

}
