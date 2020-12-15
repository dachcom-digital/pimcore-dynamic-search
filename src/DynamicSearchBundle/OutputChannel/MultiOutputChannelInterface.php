<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\OutputChannel\Query\MultiSearchContainerInterface;

interface MultiOutputChannelInterface
{
    /**
     * @param MultiSearchContainerInterface $multiSearchContainer
     *
     * @return MultiSearchContainerInterface
     */
    public function getMultiSearchResult(MultiSearchContainerInterface $multiSearchContainer): MultiSearchContainerInterface;
}
