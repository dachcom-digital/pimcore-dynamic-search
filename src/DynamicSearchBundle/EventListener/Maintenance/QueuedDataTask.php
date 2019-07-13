<?php

namespace DynamicSearchBundle\EventListener\Maintenance;

use DynamicSearchBundle\Queue\DataProcessorInterface;
use Pimcore\Maintenance\TaskInterface;

class QueuedDataTask implements TaskInterface
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
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->dataProcessor->process([]);
    }
}
