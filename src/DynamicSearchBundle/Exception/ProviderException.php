<?php

namespace DynamicSearchBundle\Exception;

final class ProviderException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct(sprintf('Provider: %s', $message));
    }
}
