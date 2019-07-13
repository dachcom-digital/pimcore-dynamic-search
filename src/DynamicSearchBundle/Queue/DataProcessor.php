<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use DynamicSearchBundle\Runner\ResourceRunnerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;

class DataProcessor implements DataProcessorInterface
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
     * @var QueueManagerInterface
     */
    protected $queueManager;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @var ResourceRunnerInterface
     */
    protected $resourceRunner;

    /**
     * @param LoggerInterface         $logger
     * @param ConfigurationInterface  $configuration
     * @param QueueManagerInterface   $queueManager
     * @param LockServiceInterface    $lockService
     * @param ResourceRunnerInterface $resourceRunner
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigurationInterface $configuration,
        QueueManagerInterface $queueManager,
        LockServiceInterface $lockService,
        ResourceRunnerInterface $resourceRunner
    ) {
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->queueManager = $queueManager;
        $this->lockService = $lockService;
        $this->resourceRunner = $resourceRunner;
    }

    /**
     * @param array $options
     */
    public function process(array $options)
    {
        if ($this->queueManager->hasActiveJobs() === false) {
            return;
        }

        if ($this->lockService->isLocked(LockServiceInterface::CONTEXT_INDEXING)) {
            return;
        }

        if ($this->lockService->isLocked(LockServiceInterface::QUEUE_INDEXING)) {
            return;
        }

        $this->lockService->lock(LockServiceInterface::QUEUE_INDEXING, 'queue worker via maintenance/command');

        try {
            $this->checkJobs();
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while processing queue envelopes. Message was: %s', $e->getMessage()), 'queue', 'global');
        }

        $this->lockService->unlock(LockServiceInterface::QUEUE_INDEXING);

    }

    protected function checkJobs()
    {
        $envelopeData = $this->queueManager->getActiveEnvelopes();

        if (empty($envelopeData) || !is_array($envelopeData)) {
            return;
        }

        foreach ($envelopeData as $contextName => $contextDispatchEnvelopes) {

            if (!is_array($contextDispatchEnvelopes) || count($contextDispatchEnvelopes) === 0) {
                continue;
            }

            foreach ($contextDispatchEnvelopes as $dispatchType => $dispatchEnvelopes) {

                if (!is_array($dispatchEnvelopes) || count($dispatchEnvelopes) === 0) {
                    continue;
                }

                $contextData = $this->configuration->getContextDefinition($dispatchType, $contextName);

                try {
                    $this->dispatchResourceRunner($contextData, $dispatchType, $dispatchEnvelopes);
                } catch (\Throwable $e) {
                    $this->logger->error(
                        sprintf('Error dispatch resource runner (%s). Message was: %s', $dispatchType, $e->getMessage()),
                        'queue', $contextName
                    );
                }
            }
        }
    }

    /**
     * @param ContextDataInterface $contextData
     * @param string               $dispatchType
     * @param array                $dispatchEnvelopes
     */
    protected function dispatchResourceRunner(ContextDataInterface $contextData, string $dispatchType, array $dispatchEnvelopes)
    {
        foreach ($dispatchEnvelopes as $envelopeData) {

            /** @var Envelope $envelope */
            $envelope = $envelopeData['envelope'];
            /** @var ResourceMetaInterface $resourceMeta */
            $resourceMeta = $envelopeData['resourceMeta'];
            /** @var array $envelopeOptions */
            $envelopeOptions = $envelope->getOptions();

            // @todo: implement stack dispatcher in resource runner!

            if ($dispatchType === ContextDataInterface::CONTEXT_DISPATCH_TYPE_INSERT) {
                $this->resourceRunner->runInsert($contextData, $resourceMeta);
            } elseif ($dispatchType === ContextDataInterface::CONTEXT_DISPATCH_TYPE_UPDATE) {
                $this->resourceRunner->runUpdate($contextData, $resourceMeta);
            } elseif ($dispatchType === ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
                $this->resourceRunner->runDelete($contextData, $resourceMeta);
            }

        }
    }
}