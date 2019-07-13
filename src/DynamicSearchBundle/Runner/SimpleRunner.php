<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Exception\RuntimeException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Processor\ResourceDeletionProcessorInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use Pimcore\Model\Element\ElementInterface;

class SimpleRunner extends AbstractRunner implements SimpleRunnerInterface
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
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var ResourceDeletionProcessorInterface
     */
    protected $resourceDeletionProcessor;

    /**
     * @param LoggerInterface                    $logger
     * @param ConfigurationInterface             $configuration
     * @param DataManagerInterface               $dataManager
     * @param ResourceHarmonizerInterface        $resourceHarmonizer
     * @param ResourceDeletionProcessorInterface $resourceDeletionProcessor
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        ResourceHarmonizerInterface $resourceHarmonizer,
        ResourceDeletionProcessorInterface $resourceDeletionProcessor
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->resourceDeletionProcessor = $resourceDeletionProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function runInsert(string $contextName, $resource)
    {
        $this->runModification($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT, $resource);
    }

    /**
     * {@inheritDoc}
     */
    public function runUpdate(string $contextName, $resource)
    {
        $this->runModification($contextName, ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE, $resource);
    }

    /**
     * {@inheritDoc}
     */
    public function runDelete(string $contextName, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition(ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        $this->resourceDeletionProcessor->process($contextDefinition, $resource);

    }

    protected function runModification(string $contextName, string $contextDispatchType, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($contextDispatchType, $contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            throw new RuntimeException(sprintf('Context configuration "%s" does not exist', $contextName));
        }

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);

        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return;
        }

        foreach ($normalizedResourceStack as $normalizedDataResource) {

            $resourceMeta = $normalizedDataResource->getResourceMeta();

            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    sprintf('No valid document id for resource "%s" given. Skipping...', $resourceType),
                    'queue', $contextName
                );

                continue;
            }

            try {
                $dataProvider = $this->dataManager->getDataProvider(
                    $contextDefinition,
                    DataProviderInterface::PROVIDER_BEHAVIOUR_SINGLE_DISPATCH,
                    $resourceMeta->getResourceOptions()
                );
            } catch (ProviderException $e) {
                $this->logger->error(sprintf('Error while fetching data provider. Error was: %s.', $e->getMessage()), 'resource_runner', $contextDefinition->getName());
                continue;
            }

            $dataProvider->provideSingle($contextDefinition, $resourceMeta);

        }
    }
}