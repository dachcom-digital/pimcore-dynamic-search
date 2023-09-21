<?php

namespace DynamicSearchBundle\Exception;

final class ProviderException extends \Exception
{
    public function __construct(string $message, ?string $providerName = null, ?\Throwable $previousException = null)
    {
        $providerName = is_null($providerName) ? '' : sprintf(' (%s)', $providerName);

        parent::__construct(sprintf('Provider Error %s: %s', $providerName, $message), 0, $previousException);
    }
}
