<?php

namespace DynamicSearchBundle\Normalizer\Resource;

use DynamicSearchBundle\Transformer\Container\DocumentContainerInterface;

interface NormalizedDataResourceInterface
{
    /**
     * @return DocumentContainerInterface|null
     */
    public function getDocumentContainer();

    /**
     * @return mixed
     */
    public function getResourceId();

    /**
     * @return array
     */
    public function getOptions();
}
