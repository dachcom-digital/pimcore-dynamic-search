<?php

namespace DynamicSearchBundle\Command;

use DynamicSearchBundle\Queue\DataProcessorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueuedDataCommand extends Command
{
    /**
     * @var DataProcessorInterface
     */
    protected $dataProcessor;

    /**
     * @param DataProcessorInterface $dataProcessor
     */
    public function __construct(DataProcessorInterface $dataProcessor)
    {
        parent::__construct();
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setHidden(true)
            ->setName('dynamic-search:check-queue')
            ->setDescription('For internal use only');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dataProcessor->process([]);
    }
}
