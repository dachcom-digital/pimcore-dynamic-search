<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\OutputChannel\Query\MultiSearchContainerInterface;

interface MultiOutputChannelInterface
{
    public function getMultiSearchResult(MultiSearchContainerInterface $multiSearchContainer): MultiSearchContainerInterface;
}
