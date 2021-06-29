<?php

namespace DynamicSearchBundle\Exception\Resolver;

final class ResourceScaffolderNotFoundException extends \RuntimeException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'No valid resource scaffolder has been found to apply');
    }
}
