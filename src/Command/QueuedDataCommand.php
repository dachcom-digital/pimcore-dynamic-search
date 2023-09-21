<?php

namespace DynamicSearchBundle\Command;

use DynamicSearchBundle\Queue\DataProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueuedDataCommand extends Command
{
    protected static $defaultName = 'dynamic-search:check-queue';
    protected static $defaultDescription = 'For internal use only';

    public function __construct(protected DataProcessorInterface $dataProcessor)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->dataProcessor->process([]);

        return 0;
    }
}
