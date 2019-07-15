<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

interface QueueManagerInterface
{
    const QUEUE_IDENTIFIER = 'dynamic_search_index_queue';

    /**
     * @return mixed
     */
    public function clearQueue();

    /**
     * @return array
     */
    public function getQueuedEnvelopes();

    /**
     * @param Envelope $envelope
     */
    public function deleteEnvelope(Envelope $envelope);

    /**
     * @param string $jobId
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $metaResources
     * @param array  $options
     */
    public function addJobToQueue(string $jobId, string $contextName, string $dispatchType, array $metaResources, array $options);

    /**
     * @return bool
     */
    public function hasActiveJobs();

    /**
     * @return array|TmpStore[]
     */
    public function getActiveJobs();
}
