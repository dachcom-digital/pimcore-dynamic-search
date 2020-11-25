<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResourceNormalizerInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /***
     * @param ContextDefinitionInterface       $contextDefinition
     * @param ResourceContainerInterface $resourceContainer
     *
     * @return array|NormalizedDataResourceInterface[]
     * @throws NormalizerException
     */
    public function normalizeToResourceStack(ContextDefinitionInterface $contextDefinition, ResourceContainerInterface $resourceContainer): array;
}
