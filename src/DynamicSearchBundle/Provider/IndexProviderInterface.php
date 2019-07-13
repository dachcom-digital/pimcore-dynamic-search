<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface IndexProviderInterface extends ProviderInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param ContextDataInterface $contextData
     * @param IndexDocument        $indexDocument
     *
     * @throws ProviderException
     */
    public function processDocument(ContextDataInterface $contextData, IndexDocument $indexDocument);
}