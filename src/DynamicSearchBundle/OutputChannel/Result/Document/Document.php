<?php

namespace DynamicSearchBundle\OutputChannel\Result\Document;

use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;

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
     * @var OutputDocumentDefinitionInterface
     */
    protected $outputDocumentDefinition;

    /**
     * @param mixed                             $hit
     * @param string                            $contextName
     * @param string                            $outputChannelName
     * @param OutputDocumentDefinitionInterface $outputDocumentDefinition
     */
    public function __construct(
        $hit,
        string $contextName,
        string $outputChannelName,
        OutputDocumentDefinitionInterface $outputDocumentDefinition
    ) {
        $this->hit = $hit;
        $this->contextName = $contextName;
        $this->outputChannelName = $outputChannelName;
        $this->outputDocumentDefinition = $outputDocumentDefinition;
    }

    /**
     * {@inheritDoc}
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * {@inheritDoc}
     */
    public function getContextName()
    {
        return $this->contextName;
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
    public function getOutputDocumentDefinition()
    {
        return $this->outputDocumentDefinition;
    }

}