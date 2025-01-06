<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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

    protected static $defaultName = 'dynamic-search:run';
    protected static $defaultDescription = 'Run Dynamic Search';

    public function __construct(
        protected ContextRunnerInterface $contextRunner,
        protected LockServiceInterface $lockService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('context', 'c', InputOption::VALUE_REQUIRED, 'Only perform on specific context')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force Crawl Start'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // should we skip context indexing if queue index is running?
        // currently, we think it's not required:
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
