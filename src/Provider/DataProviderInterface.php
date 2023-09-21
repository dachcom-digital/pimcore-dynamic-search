<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataProviderInterface extends ProviderInterface
{
    public const PROVIDER_BEHAVIOUR_FULL_DISPATCH = 'full_dispatch';
    public const PROVIDER_BEHAVIOUR_SINGLE_DISPATCH = 'single_dispatch';

    public static function configureOptions(OptionsResolver $resolver): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideAll(ContextDefinitionInterface $contextDefinition): void;

    /**
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideSingle(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta): void;
}
