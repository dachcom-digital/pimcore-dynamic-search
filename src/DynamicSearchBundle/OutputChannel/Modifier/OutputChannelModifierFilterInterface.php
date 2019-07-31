<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

interface OutputChannelModifierFilterInterface
{
    /**
     * @param string      $outputChannelServiceName
     * @param string      $outputChannelName
     * @param string|null $parentOutputChannelName
     * @param array       $options
     *
     * @return mixed
     */
    public function dispatchFilter(string $outputChannelServiceName, string $outputChannelName, ?string $parentOutputChannelName, array $options);
}
