<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Queue\Data\Envelope;

interface QueueManagerInterface
{
    const QUEUE_IDENTIFIER = 'dynamic_search_index_queue';

    const ALLOWED_DISPATCH_TYPES = ['insert', 'update', 'delete'];

    /**
     * @param string $contextName
     * @param string $dispatcher
     * @param array  $options
     */
    public function addToQueue(string $contextName, string $dispatcher, array $options);

    /**
     * @return mixed
     */
    public function clearQueue();

    /**
     * @return bool
     */
    public function hasActiveJobs();

    /**
     * @return array|Envelope[]
     */
    public function getActiveJobs();

    /**
     * @param Envelope $envelope
     */
    public function deleteJob(Envelope $envelope);

}
