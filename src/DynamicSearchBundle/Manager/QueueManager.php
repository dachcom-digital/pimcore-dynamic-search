<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Processor\Harmonizer\ResourceHarmonizerInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Tool\TmpStore;

class QueueManager implements QueueManagerInterface
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
     * @var NormalizerManagerInterface
     */
    protected $normalizerManager;

    /**
     * @var ResourceHarmonizerInterface
     */
    protected $resourceHarmonizer;

    /**
     * @param LoggerInterface             $logger
     * @param ConfigurationInterface      $configuration
     * @param NormalizerManagerInterface  $normalizerManager
     * @param ResourceHarmonizerInterface $resourceHarmonizer
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        NormalizerManagerInterface $normalizerManager,
        ResourceHarmonizerInterface $resourceHarmonizer
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->normalizerManager = $normalizerManager;
        $this->resourceHarmonizer = $resourceHarmonizer;
    }

    /**
     * {@inheritdoc}
     */
    public function addToQueue(string $contextName, string $dispatchType, $resource, array $options)
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
            $envelope = $this->generateJob($contextName, $dispatchType, $resource, $options);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error while adding data to queue. Message was: %s', $e->getMessage()),
                'queue',
                $contextName
            );
        }

        if (!$envelope instanceof Envelope) {
            return;
        }

        $this->logger->debug(
            sprintf('Envelope with id %s successfully added to queue', $envelope->getId()),
            'queue',
            $contextName
        );
    }

    /**
     * {@inheritdoc}
     */
    public function clearQueue()
    {
        try {
            $activeJobs = $this->getActiveJobs();
            $this->logger->debug(sprintf('data queue cleared. Affected jobs: %d', (is_array($activeJobs) ? count($activeJobs) : 0)), 'queue', 'maintenance');
            foreach ($activeJobs as $envelope) {
                TmpStore::delete($envelope->getId());
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while clearing queue. Message was: %s', $e->getMessage()), 'queue', 'maintenance');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveJobs()
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

        if (!is_array($activeJobs)) {
            return false;
        }

        return count($activeJobs) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveJobs()
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

        if (!is_array($activeJobs)) {
            return [];
        }

        $jobs = [];
        foreach ($activeJobs as $processId) {
            $process = $this->getJob($processId);
            if (!$process instanceof TmpStore) {
                continue;
            }

            $jobs[] = $process;
        }

        return $jobs;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveEnvelopes()
    {
        $jobs = $this->getActiveJobs();

        $existingKeys = [];
        $filteredResourceStack = [];

        /*
         *
         * A resource can be added multiple times (saving an pimcore document 3 or more times in short intervals for example).
         * Only the latest resource of its kind should be used in index processing to improve performance.
         *
         * Filter Jobs:
         *
         * -> first sort jobs by date (ASC) to receive latest entries first!
         * -> create sub array for each context and dispatch type: stack[ context ][ dispatch_type ][]
         * -> only add resource once per "context_name - document_id"
         * -> only return [ resource_meta, corresponding envelope ]
         */

        usort($jobs, function ($a, $b) {
            /**
             * @var TmpStore
             * @var $b       TmpStore
             */
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }

            return $a->getDate() < $b->getDate() ? 1 : -1;
        });

        /** @var TmpStore $job */
        foreach ($jobs as $job) {
            /** @var Envelope $envelope */
            $envelope = $job->getData();
            $contextName = $envelope->getContextName();
            $dispatchType = $envelope->getDispatchType();
            $resourceMetaStack = $envelope->getResourceMetaStack();

            if (!isset($filteredResourceStack[$contextName])) {
                $filteredResourceStack[$contextName] = [];
            }
            if (!isset($filteredResourceStack[$contextName])) {
                $filteredResourceStack[$contextName][$dispatchType] = [];
            }

            foreach ($resourceMetaStack as $resourceMeta) {
                $key = sprintf('%s_%s', $contextName, $resourceMeta->getDocumentId());

                if (in_array($key, $existingKeys, true)) {
                    continue;
                }

                $filteredResourceStack[$contextName][$dispatchType][] = [
                    'resourceMeta' => $resourceMeta,
                    'envelope'     => $envelope
                ];

                $existingKeys[] = $key;
            }

            $this->deleteJob($envelope);
        }

        return $filteredResourceStack;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteJob(Envelope $envelope)
    {
        try {
            TmpStore::delete($envelope->getId());
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Could not delete queued job with id %s', $envelope->getId()), 'queue', $envelope->getContextName());
        }
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     *
     * @return Envelope
     */
    protected function generateJob(string $contextName, string $dispatchType, $resource, array $options)
    {
        $jobId = $this->getJobId();

        $normalizedResourceStack = $this->generateResourceMeta($contextName, $dispatchType, $resource);

        if ($resource instanceof ElementInterface) {
            $resourceType = sprintf('%s-%s', $resource->getType(), $resource->getId());
        } elseif (is_object($resource)) {
            $resourceType = get_class($resource);
        } else {
            $resourceType = gettype($resource);
        }

        if (count($normalizedResourceStack) === 0) {
            $this->logger->error(
                sprintf('unable to assert stack for resource "%s" no queue job will be generated.', $resourceType),
                'queue',
                $contextName
            );

            return null;
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

        $envelope = new Envelope($jobId, $contextName, $dispatchType, $metaResources, $options);

        TmpStore::add($jobId, $envelope, self::QUEUE_IDENTIFIER);

        return $envelope;
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
     * @param $processId
     *
     * @return TmpStore|null
     */
    protected function getJob($processId)
    {
        $job = null;

        try {
            $job = TmpStore::get($processId);
        } catch (\Exception $e) {
            return null;
        }

        return $job;
    }

    /**
     * @return string
     */
    protected function getJobId()
    {
        return uniqid('dynamic-search-envelope-');
    }
}
