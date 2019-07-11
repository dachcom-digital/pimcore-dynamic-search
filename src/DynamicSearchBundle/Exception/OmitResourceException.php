<?php

namespace DynamicSearchBundle\Exception;

class OmitResourceException extends \RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(\Exception $previousException = null)
    {
        parent::__construct('Resource Normalizer omitted', 0, $previousException);
    }
}
