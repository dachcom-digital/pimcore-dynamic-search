<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
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
     * @param QueueManagerInterface   $queueManager
     * @param LockServiceInterface    $lockService
     * @param ResourceRunnerInterface $resourceRunner
     */
    public function __construct(
        LoggerInterface $logger,
        QueueManagerInterface $queueManager,
        LockServiceInterface $lockService,
        ResourceRunnerInterface $resourceRunner
    ) {
        $this->logger = $logger;
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
        $envelopeData = $this->queueManager->getQueuedEnvelopes();

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

                try {
                    $this->dispatchResourceRunner($contextName, $dispatchType, $dispatchEnvelopes);
                } catch (\Throwable $e) {
                    $this->logger->error(
                        sprintf('Error dispatch resource runner (%s). Message was: %s', $dispatchType, $e->getMessage()),
                        'queue',
                        $contextName
                    );
                }
            }
        }
    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $dispatchEnvelopes
     */
    protected function dispatchResourceRunner(string $contextName, string $dispatchType, array $dispatchEnvelopes)
    {
        $resourceMetaStack = [];
        foreach ($dispatchEnvelopes as $envelopeData) {
            /** @var Envelope $envelope */
            $envelope = $envelopeData['envelope'];
            /** @var ResourceMetaInterface $resourceMeta */
            $resourceMeta = $envelopeData['resourceMeta'];
            $envelopeOptions = $envelope->getOptions();

            $resourceMetaStack[] = $resourceMeta;
        }

        if ($dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_INSERT) {
            $this->resourceRunner->runInsertStack($contextName, $resourceMetaStack);
        } elseif ($dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_UPDATE) {
            $this->resourceRunner->runUpdateStack($contextName, $resourceMetaStack);
        } elseif ($dispatchType === ContextDefinitionInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
            $this->resourceRunner->runDeleteStack($contextName, $resourceMetaStack);
        }
    }
}
