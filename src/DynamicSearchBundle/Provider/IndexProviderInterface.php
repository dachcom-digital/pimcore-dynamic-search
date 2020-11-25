<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface IndexProviderInterface extends ProviderInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param IndexDocument              $indexDocument
     *
     * @throws ProviderException
     */
    public function processDocument(ContextDefinitionInterface $contextDefinition, IndexDocument $indexDocument);
}
