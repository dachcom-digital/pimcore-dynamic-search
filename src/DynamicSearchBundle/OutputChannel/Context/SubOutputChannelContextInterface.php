<?php

namespace DynamicSearchBundle\OutputChannel\Context;

interface SubOutputChannelContextInterface extends OutputChannelContextInterface
{
    /**
     * @return string
     */
    public function getParentOutputChannelName();
}
