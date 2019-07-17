<?php

namespace DynamicSearchBundle\Processor;

use DynamicSearchBundle\Context\ContextDataInterface;
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

            $indexDocument = new IndexDocument($resourceMeta);

            $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function processByResourceMeta(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta)
    {
        $indexProvider = $this->getIndexProvider($contextData);

        $indexDocument = new IndexDocument($resourceMeta);

        $this->sendIndexDocumentToIndexProvider($contextData, $indexProvider, $indexDocument);
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
     */
    protected function sendIndexDocumentToIndexProvider(ContextDataInterface $contextData, IndexProviderInterface $indexProvider, IndexDocument $indexDocument)
    {
        $this->logger->debug(
            sprintf(
                'Index Document with id "%s" successfully generated. Used "%s" as data normalizer.',
                $indexDocument->getDocumentId(),
                $contextData->getResourceNormalizerName()
            ),
            $contextData->getDataProviderName(),
            $contextData->getName()
        );

        try {
            $indexProvider->processDocument($contextData, $indexDocument);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf(
                    'Error while executing processing index document (%s) via provider. Error was: "%s".',
                    $contextData->getContextDispatchType(),
                    $e->getMessage()
                )
            );
        }
    }
}
