<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\Event\OutputModifierEvent;

interface OutputChannelModifierActionInterface
{
    /**
     * @param string              $action
     * @param string              $outputChannelName
     * @param OutputModifierEvent $event
     *
     * @return OutputModifierEvent
     */
    public function dispatchAction(string $action, string $outputChannelName, OutputModifierEvent $event): OutputModifierEvent;
}