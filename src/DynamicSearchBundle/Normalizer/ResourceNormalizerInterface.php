<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResourceNormalizerInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /***
     * @param ContextDataInterface       $contextData
     * @param ResourceContainerInterface $resourceContainer
     *
     * @return array|NormalizedDataResourceInterface[]
     */
    public function normalizeToResourceStack(ContextDataInterface $contextData, ResourceContainerInterface $resourceContainer): array;
}