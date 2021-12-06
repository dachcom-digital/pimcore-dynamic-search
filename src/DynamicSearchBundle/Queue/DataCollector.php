<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Builder\ContextDefinitionBuilderInterface;
use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;
use DynamicSearchBundle\Validator\ResourceValidatorInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use Pimcore\Model\Element\ElementInterface;

class DataCollector implements DataCollectorInterface
{
    protected LoggerInterface $logger;
    protected ContextDefinitionBuilderInterface $contextDefinitionBuilder;
    protected ResourceHarmonizerInterface $resourceHarmonizer;
    protected ResourceValidatorInterface $resourceValidator;
    protected QueueManagerInterface $queueManager;
    protected LockServiceInterface $lockService;

    public function __construct(
        LoggerInterface $logger,
        ContextDefinitionBuilderInterface $contextDefinitionBuilder,
        ResourceHarmonizerInterface $resourceHarmonizer,
        ResourceValidatorInterface $resourceValidator,
        QueueManagerInterface $queueManager,
        LockServiceInterface $lockService
    ) {
        $this->logger = $logger;
        $this->contextDefinitionBuilder = $contextDefinitionBuilder;
        $this->resourceHarmonizer = $resourceHarmonizer;
        $this->resourceValidator = $resourceValidator;
        $this->queueManager = $queueManager;
        $this->lockService = $lockService;
    }

    public function addToGlobalQueue(string $dispatchType, mixed $resource, array $options = []): void
    {
        $contextDefinitions = $this->contextDefinitionBuilder->buildContextDefinitionStack(ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INDEX);

        if (count($contextDefinitions) === 0) {
            $this->logger->error(
                'No context configuration found. Please add them to the "dynamic_search.context" configuration nod',
                'queue',
                'global'
            );

            return;
        }

        foreach ($contextDefinitions as $contextDefinition) {
            $this->addToContextQueue($contextDefinition->getName(), $dispatchType, $resource, $options);
        }
    }

    public function addToContextQueue(string $contextName, string $dispatchType, mixed $resource, array $options = []): void
    {
        try {
            // validate and allow rewriting dispatch type and/or resource
            $resourceCandidate = $this->resourceValidator->validateResource($contextName, $dispatchType, true, false, $resource);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while validate resource candidate: %s', $e->getMessage()), 'queue', $contextName);

            return;
        }

        if ($resourceCandidate->getResource() === null) {
            $this->logger->debug(
                sprintf('Resource has been removed due to validation. Skipping...'),
                'queue',
                $contextName
            );

            return;
        }

        $resource = $resourceCandidate->getResource();
        $dispatchType = $resourceCandidate->getDispatchType();

        if (!in_array($dispatchType, ContextDefinitionInterface::ALLOWED_QUEUE_DISPATCH_TYPES, true)) {
            $this->logger->error(
                sprintf('Wrong dispatch type "%s" for queue. Allowed types are: %s', $dispatchType, join(', ', ContextDefinitionInterface::ALLOWED_QUEUE_DISPATCH_TYPES)),
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

    protected function generateJob(string $contextName, string $dispatchType, mixed $resource, array $options): void
    {
        $jobId = $this->generateJobId();

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        // check for proxy resource (deprecated)
        $proxyResource = $this->resourceValidator->checkUntrustedResourceProxy($contextName, $dispatchType, $resource);

        if ($proxyResource instanceof ProxyResourceInterface) {
            $resource = $proxyResource->hasProxyResource() ? $proxyResource->getProxyResource() : $resource;
            $dispatchType = $proxyResource->hasProxyContextDispatchType() ? $proxyResource->getProxyContextDispatchType() : $dispatchType;
        }

        // check for proxy validity (deprecated)
        $resourcedIsValid = $this->resourceValidator->validateUntrustedResource($contextName, $dispatchType, $resource);

        if ($resourcedIsValid === false) {
            $this->logger->debug(
                sprintf('[DEPRECATED] Resource has been marked as untrusted. Skipping...'),
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
     * @return array<int, NormalizedDataResourceInterface>
     */
    protected function generateResourceMeta(string $contextName, string $dispatchType, mixed $resource): array
    {
        $contextDefinition = $this->contextDefinitionBuilder->buildContextDefinition($contextName, $dispatchType);

        $normalizedResourceStack = $this->resourceHarmonizer->harmonizeUntilNormalizedResourceStack($contextDefinition, $resource);

        if ($normalizedResourceStack === null) {
            // nothing to log: done by harmonizer.
            return [];
        }

        return $normalizedResourceStack;
    }

    protected function generateJobId(): string
    {
        return uniqid('dynamic-search-envelope-', false);
    }
}
