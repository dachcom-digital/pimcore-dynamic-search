<?php

namespace DynamicSearchBundle\Command;

use DynamicSearchBundle\Command\Traits\SignalWatchTrait;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Runner\ContextRunnerInterface;
use DynamicSearchBundle\Service\LockServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchCommand extends Command
{
    use SignalWatchTrait;

    /**
     * @var ContextRunnerInterface
     */
    protected $contextRunner;

    /**
     * @var LockServiceInterface
     */
    protected $lockService;

    /**
     * @param ContextRunnerInterface $contextRunner
     * @param LockServiceInterface   $lockService
     */
    public function __construct(ContextRunnerInterface $contextRunner, LockServiceInterface $lockService)
    {
        parent::__construct();

        $this->contextRunner = $contextRunner;
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
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force Crawl Start'
            );
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

                return 0;
            }

            $this->lockService->unlock(LockServiceInterface::CONTEXT_INDEXING);
        }

        $this->lockService->lock(LockServiceInterface::CONTEXT_INDEXING, 'context indexing via command');

        try {
            if ($input->getOption('context') === null) {
                $this->contextRunner->runFullContextCreation();
            } else {
                $this->contextRunner->runSingleContextCreation($input->getOption('context'));
            }
        } catch (SilentException $e) {
            // do not raise errors in silent exception. this error has been logged already in the right channel.
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>%s. (File: %s, Line: %s)</error>', $e->getMessage(), $e->getFile(), $e->getLine()));
        }

        $this->lockService->unlock(LockServiceInterface::CONTEXT_INDEXING);

        return 0;
    }
}
