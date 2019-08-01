<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;

class OutputChannelModifierEventDispatcher
{
    /**
     * @var OutputChannelContextInterface
     */
    protected $outputChannelContext;

    /**
     * @var OutputChannelManagerInterface
     */
    protected $outputChannelManager;

    /**
     * @param OutputChannelManagerInterface $outputChannelManager
     */
    public function __construct(OutputChannelManagerInterface $outputChannelManager)
    {
        $this->outputChannelManager = $outputChannelManager;
    }

    /**
     * @param OutputChannelContextInterface $outputChannelContext
     */
    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext)
    {
        $this->outputChannelContext = $outputChannelContext;
    }

    /**
     * Action can be dispatched several times and are optional.
     *
     * @param string $action
     * @param array  $options
     *
     * @return OutputModifierEvent
     */
    public function dispatchAction(string $action, array $options)
    {
        $outputChannelServiceName = $this->outputChannelContext->getOutputChannelServiceName();

        $event = new OutputModifierEvent($options);
        $channelModifierActions = $this->outputChannelManager->getOutputChannelModifierAction($outputChannelServiceName, $action);

        foreach ($channelModifierActions as $modifierAction) {
            $event = $modifierAction->dispatchAction($action, $this->outputChannelContext->getOutputChannelAllocator(), $event);
        }

        return $event;
    }

    /**
     * Filters can be dispatched only once an at least one filter and is required.
     *
     * @param string $filterService
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function dispatchFilter(string $filterService, array $options = [])
    {
        $outputChannelServiceName = $this->outputChannelContext->getOutputChannelServiceName();

        $channelModifierFilter = $this->outputChannelManager->getOutputChannelModifierFilter($outputChannelServiceName, $filterService);

        if ($channelModifierFilter === null) {
            throw new \Exception(sprintf('output channel filter "%s" not found', $filterService));
        }

        return $channelModifierFilter->dispatchFilter($this->outputChannelContext->getOutputChannelAllocator(), $options);
    }
}
