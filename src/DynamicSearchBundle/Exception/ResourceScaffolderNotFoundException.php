<?php

namespace DynamicSearchBundle\Exception;

final class ResourceScaffolderNotFoundException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($message = null)
    {
        parent::__construct($message ?: 'No valid ResourceScaffolder has been found to apply');
    }
}
