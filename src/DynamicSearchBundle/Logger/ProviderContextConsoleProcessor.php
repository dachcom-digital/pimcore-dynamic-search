<?php

namespace DynamicSearchBundle\Logger;

class ProviderContextConsoleProcessor
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $provider = isset($record['context']['provider']) ? $record['context']['provider'] : '--';
        $contextName = isset($record['context']['contextName']) ? $record['context']['contextName'] : '--';

        $record['extra'] = [$provider, $contextName];

        return $record;
    }
}
