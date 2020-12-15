<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DocumentNormalizerInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $outputChannelName
     * @param mixed                      $data
     *
     * @return mixed
     *
     * @throws NormalizerException
     */
    public function normalize(ContextDefinitionInterface $contextDefinition, string $outputChannelName, $data);
}
