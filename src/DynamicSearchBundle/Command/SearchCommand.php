<?php

namespace DynamicSearchBundle\Command;

use DynamicSearchBundle\Command\Traits\SignalWatchTrait;

use DynamicSearchBundle\Processor\ContextWorkflowProcessorInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends Command
{
    use SignalWatchTrait;

    /**
     * @var ContextWorkflowProcessorInterface
     */
    protected $workflowProcessor;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param ContextWorkflowProcessorInterface $workflowProcessor
     * @param LockServiceInterface              $lockService
     */
    public function __construct(
        ContextWorkflowProcessorInterface $workflowProcessor,
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
        $lockKey = LockServiceInterface::RUNNING_PROCESS;

        $this->watchSignalWithLockKey($lockKey);

        if ($this->lockService->isLocked($lockKey)) {

            if ($input->getOption('force') === false) {
                $output->writeln(sprintf('<error>%s</error>', $this->lockService->getLockMessage($lockKey)));

                return;
            }

            $this->lockService->unlock($lockKey);
        }

        $this->lockService->lock($lockKey, 'user command');

        try {
            if ($input->getOption('context') === null) {
                $this->workflowProcessor->dispatchFullContextCreation();
            } else {
                $this->workflowProcessor->dispatchSingleContextCreation($input->getOption('context'));
            }
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>%s. (File: %s, Line: %s)</error>', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        $this->lockService->unlock($lockKey);

    }
}
