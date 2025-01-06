<?php

namespace DynamicSearchBundle\Resource;

use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldTransformerInterface
{
    public function configureOptions(OptionsResolver $resolver): void;

    public function setOptions(array $options): void;

    public function transformData(string $dispatchTransformerName, ResourceContainerInterface $resourceContainer): mixed;
}
