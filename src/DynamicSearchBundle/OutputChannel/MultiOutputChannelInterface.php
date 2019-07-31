<?php

namespace DynamicSearchBundle\OutputChannel;

interface MultiOutputChannelInterface
{
    /**
     * @param string $subOutputChannelIdentifier
     * @param mixed  $subQuery
     */
    public function addSubQuery(string $subOutputChannelIdentifier, $subQuery);

    /**
     * @return array
     */
    public function getMultiSearchResult(): array;
}
