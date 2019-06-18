<?php

namespace DynamicSearchBundle\Exception;

final class DispatchTransformerNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid DispatchTransformer has been found to apply');
    }
}
