<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

class QueueManager implements QueueManagerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
    public function getQueuedEnvelopes()
    {
        $jobs = $this->getActiveJobs();

        $existingKeys = [];
        $filteredResourceStack = [];

        /*
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
             * @var TmpStore $a
             * @var TmpStore $b
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

            $this->deleteEnvelope($envelope);
        }

        return $filteredResourceStack;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteEnvelope(Envelope $envelope)
    {
        try {
            TmpStore::delete($envelope->getId());
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Could not delete queued job with id %s', $envelope->getId()), 'queue', $envelope->getContextName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addJobToQueue(string $jobId, string $contextName, string $dispatchType, array $metaResources, array $options)
    {
        $envelope = new Envelope($jobId, $contextName, $dispatchType, $metaResources, $options);

        TmpStore::add($jobId, $envelope, self::QUEUE_IDENTIFIER);
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
     * @param string $processId
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

}
