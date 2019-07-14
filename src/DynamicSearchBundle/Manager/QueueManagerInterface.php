<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

interface QueueManagerInterface
{
    const QUEUE_IDENTIFIER = 'dynamic_search_index_queue';

    /**
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     */
    public function addToGlobalQueue(string $dispatchType, $resource, array $options);

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param mixed  $resource
     * @param array  $options
     */
    public function addToContextQueue(string $contextName, string $dispatchType, $resource, array $options);

    /**
     * @return mixed
     */
    public function clearQueue();

    /**
     * @return bool
     */
    public function hasActiveJobs();

    /**
     * @return array|TmpStore[]
     */
    public function getActiveJobs();

    /**
     * @return array
     */
    public function getActiveEnvelopes();

    /**
     * @param Envelope $envelope
     */
    public function deleteJob(Envelope $envelope);
}
