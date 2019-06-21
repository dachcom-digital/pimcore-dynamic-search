<?php

namespace DynamicSearchBundle\Queue;

use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Processor\ContextWorkflowProcessorInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use DynamicSearchBundle\Service\LockServiceInterface;

class DataProcessor implements DataProcessorInterface
{
    /**
     * @var QueueManagerInterface
     */
    protected $queueManager;

    /**
     * @var ContextWorkflowProcessorInterface
     */
    protected $workflowProcessor;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param QueueManagerInterface             $queueManager
     * @param ContextWorkflowProcessorInterface $workflowProcessor
     * @param LockServiceInterface              $lockService
     */
    public function __construct(
        QueueManagerInterface $queueManager,
        ContextWorkflowProcessorInterface $workflowProcessor,
        LockServiceInterface $lockService
    ) {
        $this->queueManager = $queueManager;
        $this->workflowProcessor = $workflowProcessor;
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
            // fail silently
        }

        $this->lockService->unlock(LockServiceInterface::QUEUE_INDEXING);

    }

    protected function checkJobs()
    {
        $envelopes = $this->queueManager->getActiveJobs();

        if (empty($envelopes) || !is_array($envelopes)) {
            return;
        }

        foreach ($envelopes as $envelope) {

            $method = $this->getDispatchMethod($envelope->getDispatcher());

            if (!is_string($method)) {
                continue;
            }

            try {
                call_user_func_array([$this->workflowProcessor, $method], [$envelope->getContextName(), $envelope->getOptions()]);
            } catch (\Throwable $e) {
                // test
            }

            $this->queueManager->deleteJob($envelope);
        }
    }

    /**
     * @param string $dispatcher
     *
     * @return string|null
     */
    protected function getDispatchMethod(string $dispatcher)
    {
        switch ($dispatcher) {
            case 'insert':
                return 'dispatchInsert';
            case 'update' :
                return 'dispatchUpdate';
            case  'delete':
                return 'dispatchDeletion';
        }

        return null;
    }
}