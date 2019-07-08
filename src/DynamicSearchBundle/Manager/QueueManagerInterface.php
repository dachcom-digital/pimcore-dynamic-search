<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Tool\TmpStore;

interface QueueManagerInterface
{
    const QUEUE_IDENTIFIER = 'dynamic_search_index_queue';

    const ALLOWED_QUEUE_TYPES = ['document', 'object', 'asset'];

    /**
     * @param string $contextName
     * @param string $dispatcher
     * @param string $resourceType
     * @param int    $resourceId
     * @param array  $options
     */
    public function addToQueue(string $contextName, string $dispatcher, string $resourceType, int $resourceId, array $options);

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
     * @return array|Envelope[]
     */
    public function getActiveEnvelopes();

    /**
     * @param Envelope $envelope
     */
    public function deleteJob(Envelope $envelope);

    /**
     * @param string $resourceType
     * @param int    $resourceId
     *
     * @return ElementInterface|null
     */
    public function getResource(string $resourceType, int $resourceId);

}
