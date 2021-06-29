<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

interface QueueManagerInterface
{
    public const QUEUE_IDENTIFIER = 'dynamic_search_index_queue';

    public function clearQueue(): void;

    public function getQueuedEnvelopes(): array;

    public function deleteEnvelope(Envelope $envelope): void;

    public function addJobToQueue(
        string $jobId,
        string $contextName,
        string $dispatchType,
        array $metaResources,
        array $options
    ): void;

    public function hasActiveJobs(): bool;

    /**
     * @return TmpStore[]
     */
    public function getActiveJobs(): array;
}
