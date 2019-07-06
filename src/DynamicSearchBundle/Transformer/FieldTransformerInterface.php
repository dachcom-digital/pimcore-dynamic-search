<?php

namespace DynamicSearchBundle\Transformer;

use DynamicSearchBundle\Transformer\Container\ResourceContainerInterface;
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
     * @param array $options
     *
     * @return mixed
     */
    public function setOptions(array $options);

    /**
     * @param string                     $dispatchTransformerName
     * @param ResourceContainerInterface $resourceContainer
     *
     * @return mixed|null
     */
    public function transformData(string $dispatchTransformerName, ResourceContainerInterface $resourceContainer);
}