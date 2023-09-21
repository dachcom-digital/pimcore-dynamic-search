<?php

namespace DynamicSearchBundle\Exception;

final class OutputChannelException extends \Exception
{
    public function __construct(string $type, string $message, ?\Exception $previousException = null)
    {
        parent::__construct(sprintf('Output Channel "%s" Exception: %s', $type, $message), 0, $previousException);
    }
}
