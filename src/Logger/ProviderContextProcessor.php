<?php

namespace DynamicSearchBundle\Logger;

use Monolog\LogRecord;

class ProviderContextProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        $record['extra']['provider'] = $record['context']['provider'] ?? '--';
        $record['extra']['contextName'] = $record['context']['contextName'] ?? '--';

        return $record;
    }
}
