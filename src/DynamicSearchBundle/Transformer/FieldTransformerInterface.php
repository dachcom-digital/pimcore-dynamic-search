<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Transformer\Container\DataContainerInterface;
use DynamicSearchBundle\Transformer\Container\FieldContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FieldTransformerInterface
{
    /**
     * Return boolean false if you don't want to involve the options resolver.
     *
     * @param OptionsResolver $resolver
     *
     * @return void|bool
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param array                  $options
     * @param string                 $dispatchTransformerName
     * @param DataContainerInterface $transformedData
     *
     * @return FieldContainerInterface|null
     */
    public function transformData(array $options, string $dispatchTransformerName, DataContainerInterface $transformedData): ?FieldContainerInterface;
}