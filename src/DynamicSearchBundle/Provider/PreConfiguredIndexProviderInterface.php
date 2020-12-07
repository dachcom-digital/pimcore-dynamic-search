<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Document\IndexDocument;

interface PreConfiguredIndexProviderInterface
{
    /**
     * @param IndexDocument $indexDocument
     */
    public function preConfigureIndex(IndexDocument $indexDocument);
}
