<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\Normalizer\Resource\NormalizedDataResourceInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResourceNormalizerInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    public function setOptions(array $options): void;

    /**
     * @return array<int, NormalizedDataResourceInterface>
     * @throws NormalizerException
     */
    public function normalizeToResourceStack(ContextDefinitionInterface $contextDefinition, ResourceContainerInterface $resourceContainer): array;
}
