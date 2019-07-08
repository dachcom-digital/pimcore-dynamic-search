<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DocumentNormalizerInterface
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
     * @param ContextDataInterface $contextData
     * @param string               $outputChannelName
     * @param mixed                $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function normalize(ContextDataInterface $contextData, string $outputChannelName, $data);
}