<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Runner\ResourceRunnerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;

class DataProcessor implements DataProcessorInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected QueueManagerInterface $queueManager,
        protected LockServiceInterface $lockService,
        protected ResourceRunnerInterface $resourceRunner
    ) {
    }

    public function process(array $options): void
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

    protected function checkJobs(): void
    {
        $envelopeData = $this->queueManager->getQueuedEnvelopes();

        if (empty($envelopeData)) {
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
                } catch (SilentException $e) {
                    // do not raise errors in silent exception. this error has been logged already in the right channel.
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
     * @throws SilentException
     */
    protected function dispatchResourceRunner(string $contextName, string $dispatchType, array $dispatchEnvelopes): void
    {
        $resourceMetaStack = [];
        foreach ($dispatchEnvelopes as $envelopeData) {
            /** @var ResourceMetaInterface $resourceMeta */
            $resourceMeta = $envelopeData['resourceMeta'];
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
