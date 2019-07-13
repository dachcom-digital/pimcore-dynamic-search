<?php

namespace DynamicSearchBundle\Runner;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\DataManagerInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;

class SimpleRunner extends AbstractRunner implements SimpleRunnerInterface
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
     * @var DataManagerInterface
     */
    protected $dataManager;

    /**
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var QueueManagerInterface
     */
    protected $queueManager;

    /**
     * @var bool
     */
    protected $validProcessRunning;

    /**
     * @param LoggerInterface        $logger
     * @param ConfigurationInterface $configuration
     * @param DataManagerInterface   $dataManager
     * @param IndexManagerInterface  $indexManager
     * @param QueueManagerInterface  $queueManager
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        DataManagerInterface $dataManager,
        IndexManagerInterface $indexManager,
        QueueManagerInterface $queueManager
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->dataManager = $dataManager;
        $this->indexManager = $indexManager;
        $this->queueManager = $queueManager;
    }

    public function runInsert($resource)
    {
    }

    public function runUpdate($resource)
    {
    }

    public function runDelete($resource)
    {
    }

}