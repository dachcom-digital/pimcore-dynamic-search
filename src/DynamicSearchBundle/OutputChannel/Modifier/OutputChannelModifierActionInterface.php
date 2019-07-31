<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

use DynamicSearchBundle\Event\OutputModifierEvent;

interface OutputChannelModifierActionInterface
{
    /**
     * @param string              $action
     * @param string              $outputChannelServiceName
     * @param string              $outputChannelName
     * @param string|null         $parentOutputChannelName
     * @param OutputModifierEvent $event
     *
     * @return OutputModifierEvent
     */
    public function dispatchAction(
        string $action,
        string $outputChannelServiceName,
        string $outputChannelName,
        ?string $parentOutputChannelName,
        OutputModifierEvent $event
    ): OutputModifierEvent;
}
