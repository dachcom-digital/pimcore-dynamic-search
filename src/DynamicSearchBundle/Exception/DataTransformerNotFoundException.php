<?php

namespace DynamicSearchBundle\Exception;

final class DataTransformerNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid Transform has been found to apply');
    }
}
