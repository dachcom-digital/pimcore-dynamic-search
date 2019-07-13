<?php

namespace DynamicSearchBundle\OutputChannel\Modifier;

interface OutputChannelModifierFilterInterface
{
    /**
     * @param string $outputChannelName
     * @param array  $options
     *
     * @return mixed
     */
    public function dispatchFilter(string $outputChannelName, array $options);
}
