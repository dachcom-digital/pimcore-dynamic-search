<?php

namespace DynamicSearchBundle\Exception\Resolver;

final class ResourceScaffolderNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid resource scaffolder has been found to apply');
    }
}
