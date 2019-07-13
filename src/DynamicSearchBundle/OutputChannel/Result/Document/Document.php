<?php

namespace DynamicSearchBundle\OutputChannel\Result\Document;

class Document implements DocumentInterface
{
    /**
     * @var mixed
     */
    protected $hit;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $outputChannelName;

    /**
     * @param mixed  $hit
     * @param string $contextName
     * @param string $outputChannelName
     */
    public function __construct(
        $hit,
        string $contextName,
        string $outputChannelName
    ) {
        $this->hit = $hit;
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
    }

    /**
     * {@inheritdoc}
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextName()
    {
        return $this->contextName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputChannelName()
    {
        return $this->outputChannelName;
    }
}
