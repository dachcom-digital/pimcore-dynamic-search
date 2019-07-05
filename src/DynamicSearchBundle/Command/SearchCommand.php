<?php

namespace DynamicSearchBundle\Command;

use DynamicSearchBundle\Command\Traits\SignalWatchTrait;

use DynamicSearchBundle\Processor\ContextProcessorInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends Command
{
    use SignalWatchTrait;

    /**
     * @var ContextProcessorInterface
     */
    protected $workflowProcessor;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param ContextProcessorInterface $workflowProcessor
     * @param LockServiceInterface      $lockService
     */
    public function __construct(
        ContextProcessorInterface $workflowProcessor,
        LockServiceInterface $lockService
    ) {
        parent::__construct();

        $this->workflowProcessor = $workflowProcessor;
        $this->lockService = $lockService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dynamic-search:run')
            ->setDescription('Run Dynamic Search')
            ->addOption('context', 'c', InputOption::VALUE_REQUIRED, 'Only perform on specific context')
            ->addOption('force', 'f',
                InputOption::VALUE_NONE,
                'Force Crawl Start');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // should we skip context indexing if queue index is running?
        // currently we think it's not required:
        // queue update is modifying stable index while the context cycle will generate a new one.

        // if ($this->lockService->isLocked(LockServiceInterface::QUEUE_INDEXING)) {
        //     $output->writeln(sprintf('<error>%s</error>', $this->lockService->getLockMessage(LockServiceInterface::QUEUE_INDEXING)));
        //     return;
        // }

        $this->watchSignalWithLockKey(LockServiceInterface::CONTEXT_INDEXING);

        if ($this->lockService->isLocked(LockServiceInterface::CONTEXT_INDEXING)) {

            if ($input->getOption('force') === false) {
                $output->writeln(sprintf('<error>%s</error>', $this->lockService->getLockMessage(LockServiceInterface::CONTEXT_INDEXING)));

                return;
            }

            $this->lockService->unlock(LockServiceInterface::CONTEXT_INDEXING);
        }

        $this->lockService->lock(LockServiceInterface::CONTEXT_INDEXING, 'context indexing via command');

        try {
            if ($input->getOption('context') === null) {
                $this->workflowProcessor->dispatchFullContextCreation();
            } else {
                $this->workflowProcessor->dispatchSingleContextCreation($input->getOption('context'));
            }
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>%s. (File: %s, Line: %s)</error>', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        $this->lockService->unlock(LockServiceInterface::CONTEXT_INDEXING);

    }
}
