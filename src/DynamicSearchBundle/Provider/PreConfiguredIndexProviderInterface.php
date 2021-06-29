<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\ProviderException;

interface PreConfiguredIndexProviderInterface
{
    public function preConfigureIndex(IndexDocument $indexDocument): void;
}
