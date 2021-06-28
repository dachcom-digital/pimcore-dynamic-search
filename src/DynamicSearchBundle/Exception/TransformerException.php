<?php

namespace DynamicSearchBundle\Exception;

final class TransformerException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct(sprintf('Error while executing transformer. Message was: %s', $message));
    }
}
