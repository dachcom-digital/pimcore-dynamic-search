<?php

namespace DynamicSearchBundle\Processor\SubProcessor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Document\IndexDocumentDefinitionBuilderInterface;
use DynamicSearchBundle\Document\IndexDocumentDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexDocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\ResourceNormalizerManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;
use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;
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
     * @var IndexDocumentDefinitionManagerInterface
     */
    protected $indexDocumentDefinitionManager;

    /**
     * @var bool
     */
    protected $validProcessRunning;

    /**
     * @param LoggerInterface                         $logger
     * @param ConfigurationInterface                  $configuration
     * @param TransformerManagerInterface             $transformerManager
     * @param IndexManagerInterface                   $indexManager
     * @param ResourceNormalizerManagerInterface      $resourceNormalizerManager
     * @param IndexDocumentDefinitionManagerInterface $indexDocumentDefinitionManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager,
        ResourceNormalizerManagerInterface $resourceNormalizerManager,
        IndexDocumentDefinitionManagerInterface $indexDocumentDefinitionManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
        $this->resourceNormalizerManager = $resourceNormalizerManager;
        $this->indexDocumentDefinitionManager = $indexDocumentDefinitionManager;
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
            // error!
            return;
        }

        $transformedDocumentContainer = $documentTransformerContainer->getTransformer()->transformData($contextData, $resource);
        if (!$transformedDocumentContainer instanceof DocumentContainerInterface) {
            // error!
            return;
        }

        $resourceNormalizer = $this->resourceNormalizerManager->getResourceNormalizer($contextData);
        if (!$resourceNormalizer instanceof ResourceNormalizerInterface) {
            // error!
            return;
        }

        $normalizedResourceStack = $resourceNormalizer->normalizeToResourceStack($contextData, $transformedDocumentContainer);

        foreach ($normalizedResourceStack as $normalizedResource) {

            if (!$normalizedResource instanceof NormalizedDataResourceInterface) {
                // error!
                continue;
            }

            $indexDocumentDefinitionBuilder = $this->indexDocumentDefinitionManager->getIndexDocumentDefinitionBuilder($contextData);
            if (!$indexDocumentDefinitionBuilder instanceof IndexDocumentDefinitionBuilderInterface) {
                // error!
                continue;
            }

            $indexDocumentDefinition = $indexDocumentDefinitionBuilder->buildDefinition($normalizedResource);

            $indexDocument = $this->generateIndexDocument(
                $normalizedResource->getResourceId(),
                $contextData,
                $indexDocumentDefinition,
                $normalizedResource->getDocumentContainer(),
                $documentTransformerContainer->getIdentifier()
            );

            if (!$indexDocument instanceof IndexDocument) {
                $this->logger->error(
                    'Index Document invalid. Maybe there is no valid id field in document? Skipping...',
                    $contextData->getIndexProviderName(), $contextData->getName()
                );
                continue;
            }

            $this->logger->debug(
                sprintf('Index Document with %d fields successfully generated. Used "%s" transformer',
                    count($indexDocument->getFields()),
                    $indexDocument->getDispatchedTransformerName()
                ), $contextData->getIndexProviderName(), $contextData->getName());

            try {
                $contextData->updateRuntimeValue('index_document', $indexDocument);
                $indexProvider->execute($contextData);
            } catch (\Throwable $e) {
                throw new RuntimeException(sprintf('Unable to store index document. Error was: "%s".', $e->getMessage()));
            }
        }
    }

    /**
     * @param mixed                            $documentId
     * @param ContextDataInterface             $contextData
     * @param IndexDocumentDefinitionInterface $indexDocumentDefinition
     * @param DocumentContainerInterface       $transformedDocumentContainer
     * @param string                           $dispatchTransformerName
     *
     * @return IndexDocument
     */
    protected function generateIndexDocument(
        $documentId,
        ContextDataInterface $contextData,
        IndexDocumentDefinitionInterface $indexDocumentDefinition,
        DocumentContainerInterface $transformedDocumentContainer,
        string $dispatchTransformerName
    ) {

        $transformedDocumentOptions = [];
        foreach ($indexDocumentDefinition->getDocumentDefinitions() as $documentDefinitionOptions) {

            $transformedFieldContainer = $this->dispatchTransformer($documentDefinitionOptions, $dispatchTransformerName, $transformedDocumentContainer);
            if (!$transformedFieldContainer instanceof FieldContainerInterface) {
                // error
                continue;
            }

            $transformedDocumentOptions[] = $transformedFieldContainer;
        }

        $indexDocument = new IndexDocument($documentId, $transformedDocumentOptions, $dispatchTransformerName);

        if (empty($indexDocument->getDocumentId())) {
            // error
            return null;
        }

        foreach ($indexDocumentDefinition->getFieldDefinitions() as $fieldDefinitionOptions) {

            $transformedFieldContainer = $this->dispatchTransformer($fieldDefinitionOptions, $dispatchTransformerName, $transformedDocumentContainer);
            if (!$transformedFieldContainer instanceof FieldContainerInterface) {
                // no error!
                continue;
            }

            $indexFieldBuilder = $this->indexManager->getIndexField($contextData, $transformedFieldContainer->getIndexType());
            if (!$indexFieldBuilder instanceof IndexFieldInterface) {
                // error
                continue;
            }

            $indexField = $indexFieldBuilder->build($transformedFieldContainer);
            $indexDocument->addField($indexField, $transformedFieldContainer);

        }

        return $indexDocument;
    }

    /**
     * @param array                      $options
     * @param string                     $dispatchTransformerName
     * @param DocumentContainerInterface $transformedDocumentContainer
     *
     * @return FieldContainerInterface|null
     */
    protected function dispatchTransformer(array $options, string $dispatchTransformerName, DocumentContainerInterface $transformedDocumentContainer)
    {
        $name = $options['name'];
        $fieldTransformerName = $options['transformer'];
        $fieldTransformerOptions = isset($options['transformer_options']) ? $options['transformer_options'] : [];
        $fieldTransformerIndexType = isset($options['index_type']) ? $options['index_type'] : null;

        $fieldTransformer = $this->transformerManager->getFieldTransformer($dispatchTransformerName, $fieldTransformerName, $fieldTransformerOptions);
        if (!$fieldTransformer instanceof FieldTransformerInterface) {
            return null;
        }

        $transformedFieldContainer = $fieldTransformer->transformData($dispatchTransformerName, $transformedDocumentContainer);
        if (!$transformedFieldContainer instanceof FieldContainerInterface) {
            return null;
        }

        $transformedFieldContainer->setName($name);
        $transformedFieldContainer->setIndexType($fieldTransformerIndexType);

        return $transformedFieldContainer;
    }

}
