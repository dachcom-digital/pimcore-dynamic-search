<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Logger\LoggerInterface;
use DynamicSearchBundle\Queue\Data\Envelope;
use Pimcore\Model\Tool\TmpStore;

class QueueManager implements QueueManagerInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function addToQueue(string $contextName, string $dispatcher, array $options)
    {
        $envelope = null;

        if (!in_array($dispatcher, self::ALLOWED_DISPATCH_TYPES)) {
            $this->logger->error(
                sprintf('Wrong dispatch type "%s" for queue. Allowed types are: %s', $dispatcher, join(', ', self::ALLOWED_DISPATCH_TYPES)),
                'queue',
                $contextName
            );
            return;
        }

        try {
            $envelope = $this->generateJob($contextName, $dispatcher, $options);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error while adding data to queue. Message was: %s', $e->getMessage()),
                'queue',
                $contextName
            );
        }

        if (!$envelope instanceof Envelope) {
            return;
        }

        $this->logger->debug(
            sprintf('Envelope with id %s successfully added to queue', $envelope->getId()),
            'queue',
            $contextName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function clearQueue()
    {
        try {
            $activeJobs = $this->getActiveJobs();
            $this->logger->debug(sprintf('data queue cleared. Affected jobs: %d', (is_array($activeJobs) ? count($activeJobs) : 0)), 'queue', 'maintenance');
            foreach ($activeJobs as $envelope) {
                TmpStore::delete($envelope->getId());
            }
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while clearing queue. Message was: %s', $e->getMessage()), 'queue', 'maintenance');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hasActiveJobs()
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

        if (!is_array($activeJobs)) {
            return false;
        }

        return count($activeJobs) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveJobs()
    {
        $activeJobs = TmpStore::getIdsByTag(self::QUEUE_IDENTIFIER);

        if (!is_array($activeJobs)) {
            return [];
        }

        $jobs = [];
        foreach ($activeJobs as $processId) {

            $process = $this->getJob($processId);
            if (!$process instanceof TmpStore) {
                continue;
            }

            $jobs[] = $process;
        }

        usort($jobs, function ($a, $b) {
            /**
             * @var $a TmpStore
             * @var $b TmpStore
             */
            return strtotime($a->getDate()) - strtotime($b->getDate());
        });

        $envelopes = array_map(function (TmpStore $job) {
            return $job->getData();
        }, $jobs);

        return $envelopes;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteJob(Envelope $envelope)
    {
        try {
            TmpStore::delete($envelope->getId());
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Could not delete queued job with id %s', $envelope->getId()), 'queue', $envelope->getContextName());
        }
    }

    /**
     * @param string $contextName
     * @param string $dispatcher
     * @param array  $options
     *
     * @return Envelope
     */
    protected function generateJob(string $contextName, string $dispatcher, array $options)
    {
        $jobId = $this->getJobId();
        $envelope = new Envelope($jobId, $contextName, $dispatcher, $options);

        TmpStore::add($jobId, $envelope, self::QUEUE_IDENTIFIER);

        return $envelope;
    }

    /**
     * @param $processId
     *
     * @return TmpStore|null
     */
    protected function getJob($processId)
    {
        $job = null;
        try {
            $job = TmpStore::get($processId);
        } catch (\Exception $e) {
            return null;
        }

        return $job;
    }

    /**
     * @return string
     */
    protected function getJobId()
    {
        return uniqid('dynamic-search-envelope-');
    }
}
