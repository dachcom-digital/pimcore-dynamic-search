<?php

namespace DynamicSearchBundle\Logger;

class ProviderContextProcessor
{
    public function __invoke(array $record): array
    {
        $record['extra']['provider'] = $record['context']['provider'] ?? '--';
        $record['extra']['contextName'] = $record['context']['contextName'] ?? '--';

        return $record;
    }
}
