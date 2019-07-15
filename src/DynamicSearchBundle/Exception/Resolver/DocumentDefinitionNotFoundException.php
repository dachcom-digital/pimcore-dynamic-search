<?php

namespace DynamicSearchBundle\Exception\Resolver;

final class DocumentDefinitionNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid document definitions have been found to apply');
    }
}
