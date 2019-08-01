<?php

namespace DynamicSearchBundle\OutputChannel\Context;

class SubOutputChannelContext extends OutputChannelContext implements SubOutputChannelContextInterface
{
    /**
     * @var string
     */
    protected $parentOutputChannelName;

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
