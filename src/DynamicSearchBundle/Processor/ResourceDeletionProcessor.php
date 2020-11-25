<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;

class ResourceDeletionProcessor implements ResourceDeletionProcessorInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @param LoggerInterface             $logger
     * @param IndexManagerInterface       $indexManager
     * @param ResourceHarmonizerInterface $resourceHarmonizer
     */
    public function __construct(
        LoggerInterface $logger,
        IndexManagerInterface $indexManager,
        ResourceHarmonizerInterface $resourceHarmonizer
    ) {
        $this->logger = $logger;
        $this->indexManager = $indexManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
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

            $indexDocument = new IndexDocument($resourceMeta);

            $this->sendIndexDocumentToIndexProvider($contextDefinition, $indexProvider, $indexDocument);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processByResourceMeta(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta)
    {
        $indexProvider = $this->getIndexProvider($contextDefinition);

        $indexDocument = new IndexDocument($resourceMeta);

        $this->sendIndexDocumentToIndexProvider($contextDefinition, $indexProvider, $indexDocument);
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

    /**
     * @param ContextDefinitionInterface   $contextDefinition
     * @param IndexProviderInterface $indexProvider
     * @param IndexDocument          $indexDocument
     */
    protected function sendIndexDocumentToIndexProvider(ContextDefinitionInterface $contextDefinition, IndexProviderInterface $indexProvider, IndexDocument $indexDocument)
    {
        $this->logger->debug(
            sprintf(
                'Index Document with id "%s" successfully generated. Used "%s" as data normalizer.',
                $indexDocument->getDocumentId(),
                $contextDefinition->getResourceNormalizerName()
            ),
            $contextDefinition->getDataProviderName(),
            $contextDefinition->getName()
        );

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
}
