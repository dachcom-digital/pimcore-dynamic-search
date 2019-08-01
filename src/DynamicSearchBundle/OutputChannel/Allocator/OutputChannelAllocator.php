<?php

namespace DynamicSearchBundle\OutputChannel\Allocator;

class OutputChannelAllocator implements OutputChannelAllocatorInterface
{
    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @var string
     */
    protected $parentOutputChannelName;

    /**
     * @var string
     */
    protected $subOutputChannelIdentifier;

    /**
     * @param string      $outputChannelName
     * @param string|null $parentOutputChannelName
     * @param string|null $subOutputChannelIdentifier
     */
    public function __construct(
        string $outputChannelName,
        ?string $parentOutputChannelName,
        ?string $subOutputChannelIdentifier
    ) {
        $this->outputChannelName = $outputChannelName;
        $this->parentOutputChannelName = $parentOutputChannelName;
        $this->subOutputChannelIdentifier = $subOutputChannelIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritDoc}
     */
    public function getParentOutputChannelName()
    {
        return $this->parentOutputChannelName;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubOutputChannelIdentifier()
    {
        return $this->subOutputChannelIdentifier;
    }
}


