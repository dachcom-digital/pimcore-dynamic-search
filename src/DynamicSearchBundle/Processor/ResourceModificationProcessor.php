<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Document\Definition\DocumentDefinitionInterface;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Guard\Validator\ResourceValidatorInterface;
use DynamicSearchBundle\Index\IndexFieldInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DocumentDefinitionManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\TransformerManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
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
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var ResourceValidatorInterface
     */
    protected $resourceValidator;

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
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param ResourceValidatorInterface         $resourceValidator
     * @param DocumentDefinitionManagerInterface $documentDefinitionManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        TransformerManagerInterface $transformerManager,
        IndexManagerInterface $indexManager,
        ResourceHarmonizerInterface $resourceHarmonizer,
        ResourceValidatorInterface $resourceValidator,
        DocumentDefinitionManagerInterface $documentDefinitionManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->transformerManager = $transformerManager;
        $this->indexManager = $indexManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->resourceValidator = $resourceValidator;
        $this->documentDefinitionManager = $documentDefinitionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextDataInterface $contextData, $resource)
    {
        $indexProvider = $this->getIndexProvider($contextData);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextData, $resource);
        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        foreach ($normalizedResourceStack as $normalizedResource) {
            if (!$normalizedResource instanceof NormalizedDataResourceInterface) {
                $this->logger->error(
                    sprintf('Normalized resource needs to be instance of %s. Skipping...', NormalizedDataResourceInterface::class),
                    $contextData->getDataProviderName(),
                    $contextData->getName()
                );

                continue;
            }

            $resourceMeta = $normalizedResource->getResourceMeta();
            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    'Unable to generate index document: No document id given. Skipping...',
                    $contextData->getDataProviderName(),
                    $contextData->getName()
                );

                continue;
            }

            $isValid = $this->resourceValidator->validate($contextData->getname(), $contextData->getContextDispatchType(), $resourceMeta, $resource);

            if ($isValid === false) {
                $this->logger->debug(
                    sprintf('Resource has been dismissed by context guard. Skipping...'),
                    $contextData->getDataProviderName(),
                    $contextData->getName()
                );
                continue;
            }

            $documentDefinition = $this->generateDocumentDefinition($contextData, $resourceMeta);
            if (!$documentDefinition instanceof DocumentDefinitionInterface) {
                // nothing to log: done by generateDocumentDefinition() method.
                continue;
            }

            $resourceContainer = $normalizedResource->getResourceContainer();
            $indexDocument = $this->generateIndexDocument($contextData, $resourceContainer, $resourceMeta, $documentDefinition);
            if (!$indexDocument instanceof IndexDocument) {
                // nothing to log: done by generateIndexDocument() method.
                continue;
            }

            $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument, $resourceContainer->getResourceScaffolderIdentifier());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processByResourceMeta(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta, $resource)
    {
        $indexProvider = $this->getIndexProvider($contextData);

        $resourceContainer = $this->resourceHarmonizer->harmonizeUntilResourceContainer($contextData, $resource);
        if (!$resourceContainer instanceof ResourceContainerInterface) {
            // nothing to log: done by harmonizer
            return null;
        }

        if (empty($resourceMeta->getDocumentId())) {
            $this->logger->error(
                'Unable to generate index document: No document id given. Skipping...',
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return;
        }

        $documentDefinition = $this->generateDocumentDefinition($contextData, $resourceMeta);
        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            // nothing to log: done by generateDocumentDefinition() method.
            return;
        }

        $indexDocument = $this->generateIndexDocument($contextData, $resourceContainer, $resourceMeta, $documentDefinition);
        if (!$indexDocument instanceof IndexDocument) {
            // nothing to log: done by generateIndexDocument() method.
            return;
        }

        $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument, $resourceContainer->getResourceScaffolderIdentifier());
    }

    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     *
     * @return DocumentDefinitionInterface|null
     */
    protected function generateDocumentDefinition(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta)
    {
        $documentDefinition = $this->documentDefinitionManager->generateDocumentDefinition($contextData, $resourceMeta);
        if (!$documentDefinition instanceof DocumentDefinitionInterface) {
            $this->logger->error(
                sprintf('No document definition generated. Probably no applicable document definition builder was found. Skipping...'),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        if (count($documentDefinition->getDocumentFieldDefinitions()) === 0) {
            $this->logger->error(
                sprintf('Document Definition does not have any defined field. Skipping...'),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        return $documentDefinition;
    }

    /**
     * @param ContextDataInterface        $contextData
     * @param ResourceContainerInterface  $resourceContainer
     * @param ResourceMetaInterface       $resourceMeta
     * @param DocumentDefinitionInterface $documentDefinition
     *
     * @return IndexDocument|null
     */
    protected function generateIndexDocument(
        ContextDataInterface $contextData,
        ResourceContainerInterface $resourceContainer,
        ResourceMetaInterface $resourceMeta,
        DocumentDefinitionInterface $documentDefinition
    ) {
        $resourceScaffolderName = $resourceContainer->getResourceScaffolderIdentifier();

        $indexDocument = new IndexDocument($resourceMeta, $documentDefinition->getDocumentConfiguration());

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
            $fieldName = $fieldDefinitionOptions['name'];
            $dataTransformerOptions = $fieldDefinitionOptions['data_transformer'];
            $indexTransformerOptions = $fieldDefinitionOptions['index_transformer'];

            $transformedData = $this->dispatchResourceFieldTransformer($dataTransformerOptions, $resourceScaffolderName, $resourceContainer);

            if ($transformedData === null) {
                // no error: transformer is allowed to refuse data
                continue;
            }

            $transformedIndexData = $this->dispatchIndexTransformer($contextData, $fieldName, $indexTransformerOptions, $transformedData);
            if ($transformedIndexData === null) {
                // no error?
                continue;
            }

            $indexFieldContainer = new IndexFieldContainer($fieldName, $indexTransformerOptions['type'], $transformedIndexData);
            $indexDocument->addIndexField($indexFieldContainer);
        }

        if (!$indexDocument instanceof IndexDocument) {
            $this->logger->error(
                sprintf('Index Document needs to be instance of %s. Skipping...', IndexDocument::class),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
        }

        if (count($indexDocument->getIndexFields()) === 0) {
            $this->logger->error(
                sprintf('Index Document "%s" does not have any index fields. Skip Indexing...', $indexDocument->getDocumentId()),
                $contextData->getIndexProviderName(),
                $contextData->getName()
            );

            return null;
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

    /**
     * @param ContextDataInterface $contextData
     *
     * @return IndexProviderInterface
     */
    protected function getIndexProvider(ContextDataInterface $contextData)
    {
        try {
            $indexProvider = $this->indexManager->getIndexProvider($contextData);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf(
                    'Unable to load index provider "%s". Error was: %s',
                    $contextData->getIndexProviderName(),
                    $e->getMessage()
                )
            );
        }

        return $indexProvider;
    }

    /**
     * @param ContextDataInterface   $contextData
     * @param IndexProviderInterface $indexProvider
     * @param IndexDocument          $indexDocument
     * @param string                 $resourceScaffolderName
     */
    protected function sendIndexDocumentToIndexProvider(
        ContextDataInterface $contextData,
        IndexProviderInterface $indexProvider,
        IndexDocument $indexDocument,
        string $resourceScaffolderName
    ) {
        $this->logger->debug(
            sprintf(
                'Index Document with %d fields successfully generated. Used "%s" as resource scaffolder and "%s" as data normalizer.',
                count($indexDocument->getIndexFields()),
                $resourceScaffolderName,
                $contextData->getResourceNormalizerName()
            ),
            $contextData->getIndexProviderName(),
            $contextData->getName()
        );

        try {
            $indexProvider->processDocument($contextData, $indexDocument);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Error while executing index modification. Error was: "%s".', $e->getMessage()));
        }
    }
}
