<?php

namespace DynamicSearchBundle\OutputChannel\Context;

class SubOutputChannelContext extends OutputChannelContext implements SubOutputChannelContextInterface
{
    /**
     * @param OutputChannelContextInterface $parent
     */
    public function __construct(OutputChannelContextInterface $parent)
    {
        $this->setRuntimeQueryProvider($parent->getRuntimeQueryProvider());
        $this->setContextDefinition($parent->getContextDefinition());
        $this->setIndexProviderOptions($parent->getIndexProviderOptions());
        $this->setOutputChannelServiceName($parent->getOutputChannelServiceName());
    }
}
