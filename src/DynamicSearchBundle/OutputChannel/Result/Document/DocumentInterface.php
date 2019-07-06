<?php

namespace DynamicSearchBundle\OutputChannel\Result\Document;

use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;

interface DocumentInterface
{
    /**
     * @return mixed
     */
    public function getHit();

    /**
     * @return string
     */
    public function getContextName();

    /**
     * @return string
     */
    public function getOutputChannelName();

    /**
     * @return OutputDocumentDefinitionInterface
     */
    public function getOutputDocumentDefinition();
}