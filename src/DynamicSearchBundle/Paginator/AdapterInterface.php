<?php

namespace DynamicSearchBundle\Paginator;

use DynamicSearchBundle\Document\Definition\OutputDocumentDefinitionInterface;
use Symfony\Component\Serializer\SerializerInterface;

interface AdapterInterface extends \Zend\Paginator\Adapter\AdapterInterface
{
    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer);

    /**
     * @param string $contextName
     */
    public function setContextName(string $contextName);

    /**
     * @param string $outputChannelName
     */
    public function setOutputChannelName(string $outputChannelName);

    /**
     * @param OutputDocumentDefinitionInterface $outputDocumentDefinition
     */
    public function setOutputDocumentDefinition(OutputDocumentDefinitionInterface $outputDocumentDefinition);
}
