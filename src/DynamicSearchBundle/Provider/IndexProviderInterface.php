<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface IndexProviderInterface extends ProviderInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    public function processDocument(ContextDefinitionInterface $contextDefinition, IndexDocument $indexDocument): void;
}
