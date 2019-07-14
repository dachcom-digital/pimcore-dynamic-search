<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;

class DataCollector implements DataCollectorInterface
{
    /**
     * @var QueueManagerInterface
     */
    protected $queueManager;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param QueueManagerInterface $queueManager
     * @param LockServiceInterface  $lockService
     */
    public function __construct(
        QueueManagerInterface $queueManager,
        LockServiceInterface $lockService
    ) {
        $this->queueManager = $queueManager;
        $this->lockService = $lockService;
    }

    /**
     * {@inheritdoc}
     */
    public function addToContextQueue(string $contextName, string $dispatchType, $resource, array $options = [])
    {
        $this->queueManager->addToContextQueue($contextName, $dispatchType, $resource, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addToGlobalQueue(string $dispatchType, $resource, array $options = [])
    {
        $this->queueManager->addToGlobalQueue($dispatchType, $resource, $options);
    }
}
