<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Validator\ResourceValidatorInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use Pimcore\Model\Element\ElementInterface;

class DataCollector implements DataCollectorInterface
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
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @var ResourceValidatorInterface
     */
    protected $resourceValidator;

    /**
     * @var QueueManagerInterface
     */
    protected $queueManager;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param LoggerInterface             $logger
     * @param ConfigurationInterface      $configuration
     * @param ResourceHarmonizerInterface $resourceHarmonizer
     * @param ResourceValidatorInterface  $resourceValidator
     * @param QueueManagerInterface       $queueManager
     * @param LockServiceInterface        $lockService
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        ResourceHarmonizerInterface $resourceHarmonizer,
        ResourceValidatorInterface $resourceValidator,
        QueueManagerInterface $queueManager,
        LockServiceInterface $lockService
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->resourceValidator = $resourceValidator;
        $this->queueManager = $queueManager;
        $this->lockService = $lockService;
    }

    /**
     * {@inheritdoc}
     */
    public function addToGlobalQueue(string $dispatchType, $resource, array $options = [])
    {
        $contextDefinitions = $this->configuration->getContextDefinitions(ContextDataInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        if (count($contextDefinitions) === 0) {
            $this->logger->error(
                'No context configuration found. Please add them to the "dynamic_search.context" configuration nod',
                'queue',
                'global'
            );

            return;
        }

        /** @var ContextDataInterface $contextDefinition */
        foreach ($contextDefinitions as $contextDefinition) {
            $this->addToContextQueue($contextDefinition->getName(), $dispatchType, $resource, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addToContextQueue(string $contextName, string $dispatchType, $resource, array $options = [])
    {
        $envelope = null;

        if (!in_array($dispatchType, ContextDataInterface::ALLOWED_QUEUE_DISPATCH_TYPES)) {
            $this->logger->error(
                sprintf('Wrong dispatch type "%s" for queue. Allowed types are: %s', $dispatchType, join(', ', ContextDataInterface::ALLOWED_QUEUE_DISPATCH_TYPES)),
                'queue',
                $contextName
            );

            return;
        }

        try {
            $this->generateJob($contextName, $dispatchType, $resource, $options);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error while adding data to queue. Message was: %s', $e->getMessage()),
                'queue',
                $contextName
            );
        }
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     */
    protected function generateJob(string $contextName, string $dispatchType, $resource, array $options)
    {
        $jobId = $this->generateJobId();

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        // check for proxy resource
        $resource = $this->resourceValidator->checkUntrustedResourceProxy($contextName, $dispatchType, $resource);

        $resourcedIsValid = $this->resourceValidator->validateUntrustedResource($contextName, $dispatchType, $resource);

        if ($resourcedIsValid === false) {
            $this->logger->debug(
                sprintf('Resource has been marked as untrusted. Skipping...'),
                'queue',
                $contextName
            );

            return;
        }

        $normalizedResourceStack = $this->generateResourceMeta($contextName, $dispatchType, $resource);

        if (count($normalizedResourceStack) === 0) {
            $this->logger->error(
                sprintf('Unable to assert stack for resource "%s". No queue job will be generated.', $resourceType),
                'queue',
                $contextName
            );

            return;
        }

        $metaResources = [];
        foreach ($normalizedResourceStack as $normalizedDataResource) {
            $resourceMeta = $normalizedDataResource->getResourceMeta();

            if (empty($resourceMeta->getDocumentId())) {
                $this->logger->error(
                    sprintf('No valid document id for resource "%s" given. Skipping...', $resourceType),
                    'queue',
                    $contextName
                );

                continue;
            }

            $metaResources[] = $resourceMeta;
        }

        if (count($metaResources) === 0) {
            return;
        }

        $this->queueManager->addJobToQueue($jobId, $contextName, $dispatchType, $metaResources, $options);

        $this->logger->debug(
            sprintf('Envelope successfully added to queue ("%s" context)', $contextName),
            'queue',
            $contextName
        );
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     *
     * @return array|NormalizedDataResourceInterface[]
     */
    protected function generateResourceMeta(string $contextName, string $dispatchType, $resource)
    {
        $contextDefinition = $this->configuration->getContextDefinition($dispatchType, $contextName);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);

        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return [];
        }

        return $normalizedResourceStack;
    }

    /**
     * @return string
     */
    protected function generateJobId()
    {
        return uniqid('dynamic-search-envelope-');
    }
}
