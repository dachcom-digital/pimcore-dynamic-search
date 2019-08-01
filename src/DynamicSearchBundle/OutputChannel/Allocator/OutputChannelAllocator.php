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
     * {@inheritdoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentOutputChannelName()
    {
        return $this->parentOutputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubOutputChannelIdentifier()
    {
        return $this->subOutputChannelIdentifier;
    }
}
