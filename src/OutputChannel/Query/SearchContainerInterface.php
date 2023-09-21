<?php

namespace DynamicSearchBundle\OutputChannel\Query;

use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;

/**
 * @property RawResultInterface $result
 */
interface SearchContainerInterface
{
    public function getQuery(): mixed;

    public function getIdentifier(): string;

    public function getRawResult(): RawResultInterface;
}
