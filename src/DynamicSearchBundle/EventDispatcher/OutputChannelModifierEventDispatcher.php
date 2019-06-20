<?php

namespace DynamicSearchBundle\EventDispatcher;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Event\OutputModifierEvent;
use DynamicSearchBundle\Manager\OutputChannelManagerInterface;

class OutputChannelModifierEventDispatcher
{
    /**
     * @var string
     */
    protected $outputProvider;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var ContextDataInterface
     */
    protected $contextData;

    /**
     * @var OutputChannelManagerInterface
     */
    protected $outputChannelManager;

    /**
     * @param string                        $outputProvider
     * @param string                        $outputChannelName
     * @param ContextDataInterface          $contextData
     * @param OutputChannelManagerInterface $outputChannelManager
     */
    public function __construct(string $outputProvider, string $outputChannelName, $contextData, OutputChannelManagerInterface $outputChannelManager)
    {
        $this->outputProvider = $outputProvider;
        $this->outputChannelName = $outputChannelName;
        $this->contextData = $contextData;
        $this->outputChannelManager = $outputChannelManager;
    }

    /**
     * Action can be dispatched several times and are optional
     *
     * @param string $action
     * @param array  $options
     *
     * @return OutputModifierEvent
     */
    public function dispatchAction(string $action, array $options)
    {
        $event = new OutputModifierEvent($options);
        $channelModifierAction = $this->outputChannelManager->getOutputChannelModifierAction($this->outputProvider, $action);

        foreach ($channelModifierAction as $modifierAction) {
            $event = $modifierAction->dispatchAction($action, $this->outputChannelName, $event);
        }

        return $event;
    }

    /**
     * Filters can be dispatched only once an at least one filter and is required
     *
     * @param string $filterService
     * @param array  $options
     *
     * @return mixed
     * @throws \Exception
     */
    public function dispatchFilter(string $filterService, array $options = [])
    {
        $channelModifierFilter = $this->outputChannelManager->getOutputChannelModifierFilter($this->outputProvider, $filterService);

        if ($channelModifierFilter === null) {
            throw new \Exception(sprintf('output channel filter "%s" not found', $filterService));
        }

        return $channelModifierFilter->dispatchFilter($this->outputChannelName, $options);
    }
}
