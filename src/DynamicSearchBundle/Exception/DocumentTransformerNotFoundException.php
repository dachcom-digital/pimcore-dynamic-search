<?php

namespace DynamicSearchBundle\Exception;

final class DocumentTransformerNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid DispatchTransformer has been found to apply');
    }
}
