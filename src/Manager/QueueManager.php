<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

class QueueManager implements QueueManagerInterface
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function clearQueue(): void
    {
        try {
            $activeJobs = $this->getActiveJobs();
            $this->logger->debug(sprintf('data queue cleared. Affected jobs: %d', count($activeJobs)), 'queue', 'maintenance');
            foreach ($activeJobs as $envelope) {
                TmpStore::delete($envelope->getId());
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while clearing queue. Message was: %s', $e->getMessage()), 'queue', 'maintenance');
        }
    }

    public function getQueuedEnvelopes(): array
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

        usort($jobs, static function (TmpStore $a, TmpStore $b) {

             /** @var Envelope $envelopeA */
            $envelopeA = $a->getData();
             /** @var Envelope $envelopeB */
            $envelopeB = $b->getData();

            if ($envelopeA->getCreationTime() === $envelopeB->getCreationTime()) {
                return 0;
            }

            return $envelopeA->getCreationTime() < $envelopeB->getCreationTime() ? 1 : -1;
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

            if (!isset($filteredResourceStack[$contextName][$dispatchType])) {
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

    public function deleteEnvelope(Envelope $envelope): void
    {
        try {
            TmpStore::delete($envelope->getId());
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Could not delete queued job with id %s', $envelope->getId()), 'queue', $envelope->getContextName());
        }
    }

    public function addJobToQueue(string $jobId, string $contextName, string $dispatchType, array $metaResources, array $options): void
    {
        $envelope = new Envelope($jobId, $contextName, $dispatchType, $metaResources, $options, microtime(true));

        TmpStore::add($jobId, $envelope, self::QUEUE_IDENTIFIER);
    }

    public function hasActiveJobs(): bool
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

        return count($activeJobs) > 0;
    }

    public function getActiveJobs(): array
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

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

    protected function getJob(string $processId): ?TmpStore
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
