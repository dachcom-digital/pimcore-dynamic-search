<?php

namespace DynamicSearchBundle\OutputChannel\Context;

class SubOutputChannelContext extends OutputChannelContext implements SubOutputChannelContextInterface
{
    /**
     * @var string
     */
    protected $parentOutputChannelName;

    /**
     * @param OutputChannelContext $parent
     */
    public function __construct(OutputChannelContext $parent)
    {
        $this->setContextDefinition($parent->getContextDefinition());
        $this->setRuntimeOptionsProvider($parent->getRuntimeOptionsProvider());
        $this->setIndexProviderOptions($parent->getIndexProviderOptions());
        $this->setOutputChannelServiceName($parent->getOutputChannelServiceName());
    }

    /**
     * @param string $parentOutputChannelName
     */
    public function setParentOutputChannelName(string $parentOutputChannelName)
    {
        $this->parentOutputChannelName = $parentOutputChannelName;
    }

    /**
     * @return string
     */
    public function getParentOutputChannelName()
    {
        return $this->parentOutputChannelName;
    }

}
