<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;

class OutputChannelModifierEventDispatcher
{
    protected OutputChannelManagerInterface $outputChannelManager;
    protected ?OutputChannelContextInterface $outputChannelContext = null;

    public function __construct(OutputChannelManagerInterface $outputChannelManager)
    {
        $this->outputChannelManager = $outputChannelManager;
    }

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void
    {
        $this->outputChannelContext = $outputChannelContext;
    }

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
     * @return mixed
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
