<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;
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

    /**
     * @param ResourceIdBuilderInterface $resourceIdBuilder
     */
    public function setIdBuilder(ResourceIdBuilderInterface $resourceIdBuilder);

    /**
     * @return ResourceIdBuilderInterface
     */
    public function getIdBuilder();

    /***
     * @param ContextDataInterface       $contextData
     * @param DocumentContainerInterface $documentContainer
     *
     * @return array|NormalizedDataResourceInterface[]
     */
    public function normalizeToResourceStack(ContextDataInterface $contextData, DocumentContainerInterface $documentContainer): array;
}