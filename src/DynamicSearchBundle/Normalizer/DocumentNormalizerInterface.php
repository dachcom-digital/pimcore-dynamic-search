<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\NormalizerException;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
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
     * @param RawResultInterface         $rawResult
     * @param ContextDefinitionInterface $contextDefinition
     * @param string                     $outputChannelName
     *
     * @return mixed
     *
     * @throws NormalizerException
     */
    public function normalize(RawResultInterface $rawResult, ContextDefinitionInterface $contextDefinition, string $outputChannelName);
}
