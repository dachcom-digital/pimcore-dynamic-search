<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;

interface PreConfiguredIndexProviderInterface
{
    /**
     * @param IndexDocument $indexDocument
     *
     * @throws ProviderException
     */
    public function preConfigureIndex(IndexDocument $indexDocument);
}
