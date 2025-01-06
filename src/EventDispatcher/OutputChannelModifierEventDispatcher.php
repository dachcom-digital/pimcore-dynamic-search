<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;

class OutputChannelModifierEventDispatcher
{
    protected OutputChannelContextInterface $outputChannelContext;
    protected OutputChannelManagerInterface $outputChannelManager;

    public function __construct(OutputChannelManagerInterface $outputChannelManager)
    {
        $this->outputChannelManager = $outputChannelManager;
    }

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void
    {
        $this->outputChannelContext = $outputChannelContext;
    }

    /**
     * Action can be dispatched several times and are optional
     */
    public function dispatchAction(string $action, array $options): OutputModifierEvent
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
     * Filters can be dispatched only once and at least one filter is required
     *
     * @throws \Exception
     */
    public function dispatchFilter(string $filterService, array $options = []): mixed
    {
        $outputChannelServiceName = $this->outputChannelContext->getOutputChannelServiceName();
        $channelModifierFilter = $this->outputChannelManager->getOutputChannelModifierFilter($outputChannelServiceName, $filterService);

        if ($channelModifierFilter === null) {
            throw new \Exception(sprintf('output channel filter "%s" not found', $filterService));
        }

        return $channelModifierFilter->dispatchFilter($this->outputChannelContext->getOutputChannelAllocator(), $options);
    }
}
