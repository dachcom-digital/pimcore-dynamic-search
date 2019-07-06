<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DocumentDefinitionBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @param IndexDocumentDefinitionInterface $definition
     * @param NormalizedDataResourceInterface  $dataResource
     *
     * @return IndexDocumentDefinitionInterface
     */
    public function buildInputDefinition(IndexDocumentDefinitionInterface $definition, NormalizedDataResourceInterface $dataResource): IndexDocumentDefinitionInterface;

    /**
     * @param OutputDocumentDefinitionInterface $definition
     *
     * @return OutputDocumentDefinitionInterface
     */
    public function buildOutputDefinition(OutputDocumentDefinitionInterface $definition): OutputDocumentDefinitionInterface;

}