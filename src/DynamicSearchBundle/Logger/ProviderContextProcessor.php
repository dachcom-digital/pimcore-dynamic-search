<?php

namespace DynamicSearchBundle\Logger;

class ProviderContextProcessor
{
    /**
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        $record['extra']['provider'] = isset($record['context']['provider']) ? $record['context']['provider'] : '--';
        $record['extra']['contextName'] = isset($record['context']['contextName']) ? $record['context']['contextName'] : '--';

        return $record;
    }
}
