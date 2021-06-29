<?php

namespace DynamicSearchBundle\EventListener\Maintenance;

use DynamicSearchBundle\Queue\DataProcessorInterface;
use Pimcore\Maintenance\TaskInterface;

class QueuedDataTask implements TaskInterface
{
    protected DataProcessorInterface $dataProcessor;

    public function __construct(DataProcessorInterface $dataProcessor)
    {
        $this->dataProcessor = $dataProcessor;
    }

    public function execute(): void
    {
        $this->dataProcessor->process([]);
    }
}
