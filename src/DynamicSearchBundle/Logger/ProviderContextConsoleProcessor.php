<?php

namespace DynamicSearchBundle\Logger;

class ProviderContextConsoleProcessor
{
    public function __invoke(array $record): array
    {
        $provider = $record['context']['provider'] ?? '--';
        $contextName = $record['context']['contextName'] ?? '--';

        $record['extra'] = [$provider, $contextName];

        return $record;
    }
}
