<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use DynamicSearchBundle\Registry\ContextGuardRegistryInterface;
use DynamicSearchBundle\Resource\Container\OptionFieldContainer;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use DynamicSearchBundle\Resource\Container\IndexFieldContainer;
use DynamicSearchBundle\Resource\FieldTransformerInterface;

class ResourceModificationProcessor implements ResourceModificationProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

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
     * @var ContextGuardRegistryInterface
     */
    protected $contextGuardRegistry;

    /**
     * @param LoggerInterface                    $logger
     * @param ContextDefinitionBuilderInterface  $contextDefinitionBuilder
     * @param TransformerManagerInterface        $transformerManager
     * @param IndexManagerInterface              $indexManager
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     * @param ContextGuardRegistryInterface      $contextGuardRegistry
     */
    public function __construct(
        LoggerInterface $logger,
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager,
        ResourceHarmonizerInterface $resourceHarmonizer,
        DocumentDefinitionManagerInterface $documentDefinitionManager,
        ContextGuardRegistryInterface $contextGuardRegistry
    ) {
        $this->logger = $logger;
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->documentDefinitionManager = $documentDefinitionManager;
        $this->contextGuardRegistry = $contextGuardRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextDefinitionInterface $contextDefinition, $resource)
    {
        $indexProvider = $this->getIndexProvider($contextDefinition);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);
        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        foreach ($normalizedResourceStack as $normalizedResource) {
            if (!$normalizedResource instanceof NormalizedDataResourceInterface) {
                $this->logger->error(
                    sprintf('Normalized resource needs to be instance of %s. Skipping...', NormalizedDataResourceInterface::class),
                    $contextDefinition->getDataProviderName(),
                    $contextDefinition->getName()
                );

                continue;
            }

            $resourceMeta = $normalizedResource->getResourceMeta();
            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    'Unable to generate index document: No document id given. Skipping...',
                    $contextDefinition->getDataProviderName(),
                    $contextDefinition->getName()
                );

                continue;
            }

            $approvedByContextGuard = $this->invokeContextGuard($contextDefinition->getName(), $resourceMeta);
            if ($approvedByContextGuard === false) {
                $this->logger->debug(
                    'Resource has been rejected by context guard. Skipping...',
                    $contextDefinition->getDataProviderName(),
                    $contextDefinition->getName()
                );

                continue;
            }

            $documentDefinition = $this->generateDocumentDefinition($contextDefinition, $resourceMeta);
            if (!$documentDefinition instanceof DocumentDefinitionInterface) {
                // nothing to log: done by generateDocumentDefinition() method.
                continue;
            }

            $resourceContainer = $normalizedResource->getResourceContainer();
            $indexDocument = $this->generateIndexDocument($contextDefinition, $resourceMeta, $documentDefinition);
            if (!$indexDocument instanceof IndexDocument) {
                // nothing to log: done by generateIndexDocument() method.
                continue;
            }

            $indexDocument = $this->populateIndexDocument($contextDefinition, $indexDocument, $resourceContainer, $documentDefinition);

            $this->validateAndSubmitIndexDocument($contextDefinition, $indexProvider, $indexDocument, $resourceContainer->getResourceScaffolderIdentifier());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta, $resource)
    {
        $indexProvider = $this->getIndexProvider($contextDefinition);

        $resourceContainer = $this->resourceHarmonizer->harmonizeUntilResourceContainer($contextDefinition, $resource);
        if (!$resourceContainer instanceof ResourceContainerInterface) {
            // nothing to log: done by harmonizer
            return null;
        }

        if (empty($resourceMeta->getDocumentId())) {
            $this->logger->error(
                'Unable to generate index document: No document id given. Skipping...',
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return;
        }

        $approvedByContextGuard = $this->invokeContextGuard($contextDefinition->getName(), $resourceMeta);
        if ($approvedByContextGuard === false) {
            $this->logger->debug(
                'Resource has been rejected by context guard. Skipping...',
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return;
        }

        $documentDefinition = $this->generateDocumentDefinition($contextDefinition, $resourceMeta);
        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            // nothing to log: done by generateDocumentDefinition() method.
            return;
        }

        $indexDocument = $this->generateIndexDocument($contextDefinition, $resourceMeta, $documentDefinition);
        if (!$indexDocument instanceof IndexDocument) {
            // nothing to log: done by generateIndexDocument() method.
            return;
        }

        $indexDocument = $this->populateIndexDocument($contextDefinition, $indexDocument, $resourceContainer, $documentDefinition);

        $this->validateAndSubmitIndexDocument($contextDefinition, $indexProvider, $indexDocument, $resourceContainer->getResourceScaffolderIdentifier());
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface      $resourceMeta
     *
     * @return DocumentDefinitionInterface|null
     */
    protected function generateDocumentDefinition(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta)
    {
        $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinition($contextDefinition, $resourceMeta);
        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            $this->logger->error(
                sprintf('No document definition generated. Probably no applicable document definition builder was found. Skipping...'),
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        if (count($documentDefinition->getDocumentFieldDefinitions()) === 0) {
            $this->logger->debug(
                sprintf('Document Definition does not have any defined field. Skipping...'),
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        return $documentDefinition;
    }

    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param ResourceMetaInterface       $resourceMeta
     * @param DocumentDefinitionInterface $documentDefinition
     *
     * @return IndexDocument|null
     */
    protected function generateIndexDocument(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        DocumentDefinitionInterface $documentDefinition
    ) {
        $indexDocument = new IndexDocument($resourceMeta, $documentDefinition->getDocumentConfiguration());

        if (!$indexDocument instanceof IndexDocument) {
            $this->logger->error(
                sprintf('Index Document needs to be instance of %s. Skipping...', IndexDocument::class),
                $contextDefinition->getDataProviderName(),
                $contextDefinition->getName()
            );

            return null;
        }

        return $indexDocument;
    }

    /**
     * @param ContextDefinitionInterface  $contextDefinition
     * @param IndexDocument               $indexDocument
     * @param ResourceContainerInterface  $resourceContainer
     * @param DocumentDefinitionInterface $documentDefinition
     *
     * @return IndexDocument
     */
    public function populateIndexDocument(
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
            // throw error?
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
     */
    protected function dispatchResourceFieldTransformer(array $options, string $dispatchTransformerName, ResourceContainerInterface $resourceContainer)
    {
        $fieldTransformerName = $options['type'];
        $fieldTransformerConfiguration = $options['configuration'];

        $fieldTransformer = $this->transformerManager->getResourceFieldTransformer($dispatchTransformerName, $fieldTransformerName, $fieldTransformerConfiguration);
        if (!$fieldTransformer instanceof FieldTransformerInterface) {
            return null;
        }

        $transformedData = $fieldTransformer->transformData($dispatchTransformerName, $resourceContainer);

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
     */
    protected function dispatchIndexTransformer(ContextDefinitionInterface $contextDefinition, string $indexFieldName, array $options, $transformedData)
    {
        $indexTypeName = $options['type'];
        $indexTypeConfiguration = $options['configuration'];

        $indexFieldBuilder = $this->indexManager->getIndexField($contextDefinition, $indexTypeName);
        if (!$indexFieldBuilder instanceof IndexFieldInterface) {
            return null;
        }

        $indexFieldData = $indexFieldBuilder->build($indexFieldName, $transformedData, $indexTypeConfiguration);
        if ($indexFieldData === null) {
            return null;
        }

        return $indexFieldData;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param IndexProviderInterface     $indexProvider
     * @param IndexDocument              $indexDocument
     * @param string                     $resourceScaffolderName
     */
    protected function validateAndSubmitIndexDocument(
        ContextDefinitionInterface $contextDefinition,
        IndexProviderInterface $indexProvider,
        IndexDocument $indexDocument,
        string $resourceScaffolderName
    ) {
        $logType = 'debug';
        $logMessage = null;
        $contextDispatchType = $contextDefinition->getContextDispatchType();

        if (count($indexDocument->getIndexFields()) === 0) {
            if ($contextDefinition->getContextDispatchType() === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE) {
                $contextDispatchType = ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE;
                $logMessage = sprintf('Index Document "%s" does not have any index fields. Trying to remove it from index...', $indexDocument->getDocumentId());
            } else {
                $logType = 'error';
                $contextDispatchType = null;
                $logMessage = sprintf('Index Document "%s" does not have any index fields. Skip Indexing...', $indexDocument->getDocumentId());
            }
        } else {
            $logA = sprintf('Index Document with %d fields successfully generated', count($indexDocument->getIndexFields()));
            $logB = sprintf('Used "%s" as resource scaffolder', $resourceScaffolderName);
            $logC = sprintf('Used "%s" as data normalizer', $contextDefinition->getResourceNormalizerName());
            $logMessage = sprintf('%s. %s. %s.', $logA, $logB, $logC);
        }

        $this->logger->log($logType, $logMessage, $contextDefinition->getDataProviderName(), $contextDefinition->getName());

        if ($contextDispatchType === null) {
            return;
        }

        // switch dispatch type!
        if ($contextDefinition->getContextDispatchType() !== $contextDispatchType) {
            $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextDefinition->getName(), $contextDispatchType);
        }

        $this->sendIndexDocumentToIndexProvider($contextDefinition, $indexProvider, $indexDocument);
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param IndexProviderInterface     $indexProvider
     * @param IndexDocument              $indexDocument
     */
    protected function sendIndexDocumentToIndexProvider(ContextDefinitionInterface $contextDefinition, IndexProviderInterface $indexProvider, IndexDocument $indexDocument)
    {
        try {
            $indexProvider->processDocument($contextDefinition, $indexDocument);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf(
                'Error while executing processing index document (%s) via provider. Error was: "%s".',
                $contextDefinition->getContextDispatchType(),
                $e->getMessage()
            ));
        }
    }

    /**
     * @param string                $contextName
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return bool
     */
    protected function invokeContextGuard(string $contextName, ResourceMetaInterface $resourceMeta)
    {
        foreach ($this->contextGuardRegistry->getAllGuards() as $contextGuard) {
            if ($contextGuard->verifyResourceMetaForContext($contextName, $resourceMeta) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return IndexProviderInterface
     */
    protected function getIndexProvider(ContextDefinitionInterface $contextDefinition)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextDefinition);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to load index provider "%s". Error was: %s',
                    $contextDefinition->getIndexProviderName(),
                    $e->getMessage()
                )
            );
        }

        return $indexProvider;
    }
}
