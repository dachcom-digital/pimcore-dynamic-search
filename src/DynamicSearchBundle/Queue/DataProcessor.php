<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Processor\ContextProcessorInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
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
     * @var ContextProcessorInterface
     */
    protected $contextProcessor;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param LoggerInterface           $logger
     * @param QueueManagerInterface     $queueManager
     * @param ContextProcessorInterface $contextProcessor
     * @param LockServiceInterface      $lockService
     */
    public function __construct(
        LoggerInterface $logger,
        QueueManagerInterface $queueManager,
        ContextProcessorInterface $contextProcessor,
        LockServiceInterface $lockService
    ) {
        $this->logger = $logger;
        $this->queueManager = $queueManager;
        $this->contextProcessor = $contextProcessor;
        $this->lockService = $lockService;
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
        $envelopes = $this->queueManager->getActiveEnvelopes();

        if (empty($envelopes) || !is_array($envelopes)) {
            return;
        }

        foreach ($envelopes as $contextName => $contextDispatchEnvelopes) {

            if (!is_array($contextDispatchEnvelopes) || count($contextDispatchEnvelopes) === 0) {
                continue;
            }

            foreach ($contextDispatchEnvelopes as $dispatchType => $dispatchEnvelopes) {

                if (!is_array($dispatchEnvelopes) || count($dispatchEnvelopes) === 0) {
                    continue;
                }

                try {

                    if ($dispatchType === ContextDataInterface::CONTEXT_DISPATCH_TYPE_DELETE) {
                        $this->dispatchDeletionContext($contextName, $dispatchType, $dispatchEnvelopes);
                    } else {
                        $this->dispatchModificationContext($contextName, $dispatchType, $dispatchEnvelopes);
                    }

                } catch (\Throwable $e) {
                    $this->logger->error(
                        sprintf('Error dispatch queued context (%s). Message was: %s', $dispatchType, $e->getMessage()),
                        'queue', $contextName
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
    protected function dispatchDeletionContext(string $contextName, string $dispatchType, array $dispatchEnvelopes)
    {
        $envelopeResourceStack = [
            'contextName'  => $contextName,
            'dispatchType' => $dispatchType,
            'resources'    => []
        ];

        $runtimeValues = [];

        /** @var Envelope $envelope */
        foreach ($dispatchEnvelopes as $envelope) {

            $envelopeOptions = $envelope->getOptions();
            if (!isset($envelopeOptions['removable_documents']) || !is_array($envelopeOptions['removable_documents'])) {

                $this->logger->error(
                    sprintf(
                        'Unable to dispatch envelope "%s-%s deletion because of missing resource ids.',
                        $envelope->getResourceType(), $envelope->getResourceId()
                    ),
                    'queue', $contextName
                );

                continue;

            } else {
                foreach ($envelopeOptions['removable_documents'] as $resourceMeta) {
                    $envelopeResourceStack['resources'][] = $resourceMeta;
                }
            }

            unset($envelopeOptions['removable_documents']);
            $runtimeValues = $envelopeOptions;

            $this->queueManager->deleteJob($envelope);

        }

        $this->contextProcessor->dispatchContextModificationStack(
            $envelopeResourceStack['contextName'],
            $envelopeResourceStack['dispatchType'],
            $envelopeResourceStack['resources'],
            $runtimeValues
        );

    }

    /**
     * @param string $contextName
     * @param string $dispatchType
     * @param array  $dispatchEnvelopes
     */
    protected function dispatchModificationContext(string $contextName, string $dispatchType, array $dispatchEnvelopes)
    {
        $envelopeResourceStack = [
            'contextName'  => $contextName,
            'dispatchType' => $dispatchType,
            'resources'    => []
        ];

        $runtimeValues = [];

        /** @var Envelope $envelope */
        foreach ($dispatchEnvelopes as $envelope) {
            $runtimeValues = $envelope->getOptions();
            $envelopeResourceStack['resources'][] = $this->queueManager->getResource($envelope->getResourceType(), $envelope->getResourceId());
            $this->queueManager->deleteJob($envelope);
        }

        $this->contextProcessor->dispatchContextModificationStack(
            $envelopeResourceStack['contextName'],
            $envelopeResourceStack['dispatchType'],
            $envelopeResourceStack['resources'],
            $runtimeValues
        );
    }

}