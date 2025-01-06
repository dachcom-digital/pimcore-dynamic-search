<?php

namespace DynamicSearchBundle\Logger;

use Monolog\LogRecord;

class ProviderContextConsoleProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $provider = $record['context']['provider'] ?? '--';
        $contextName = $record['context']['contextName'] ?? '--';

        $record['extra'] = [$provider, $contextName];

        return $record;
    }
}
