<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Context\SubOutputChannelContextInterface;

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
        $outputChannelName = $this->outputChannelContext->getOutputChannelName();
        $parentOutputChannelName = null;

        if ($this->outputChannelContext instanceof SubOutputChannelContextInterface) {
            $parentOutputChannelName = $this->outputChannelContext->getParentOutputChannelName();
        }

        $event = new OutputModifierEvent($options);
        $channelModifierAction = $this->outputChannelManager->getOutputChannelModifierAction($outputChannelServiceName, $action);

        foreach ($channelModifierAction as $modifierAction) {
            $event = $modifierAction->dispatchAction($action, $outputChannelServiceName, $outputChannelName, $parentOutputChannelName, $event);
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
        $outputChannelName = $this->outputChannelContext->getOutputChannelName();
        $parentOutputChannelName = null;

        if ($this->outputChannelContext instanceof SubOutputChannelContextInterface) {
            $parentOutputChannelName = $this->outputChannelContext->getParentOutputChannelName();
        }

        $channelModifierFilter = $this->outputChannelManager->getOutputChannelModifierFilter($outputChannelServiceName, $filterService);

        if ($channelModifierFilter === null) {
            throw new \Exception(sprintf('output channel filter "%s" not found', $filterService));
        }

        return $channelModifierFilter->dispatchFilter($outputChannelServiceName, $outputChannelName, $parentOutputChannelName, $options);
    }
}
