<?php

namespace DynamicSearchBundle\Normalizer;

use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;

interface ResourceIdBuilderInterface
{
    /**
     * @param array $normalizerOptions
     */
    public function setOptions(array $normalizerOptions);

    /**
     * @param DocumentContainerInterface $documentContainer
     * @param array                      $buildOptions
     *
     * @return mixed|null
     */
    public function build(DocumentContainerInterface $documentContainer, array $buildOptions = []);
}