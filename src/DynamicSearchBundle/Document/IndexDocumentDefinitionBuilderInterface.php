<?php

namespace DynamicSearchBundle\Document;

use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface IndexDocumentDefinitionBuilderInterface
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
     * @param NormalizedDataResourceInterface $dataResource
     *
     * @return IndexDocumentDefinitionInterface
     */
    public function buildDefinition(NormalizedDataResourceInterface $dataResource): IndexDocumentDefinitionInterface;
}