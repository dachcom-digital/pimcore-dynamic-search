<?php

namespace DynamicSearchBundle\OutputChannel\Query;

use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;

/**
 * @property RawResultInterface $result
 */
interface SearchContainerInterface
{
    /**
     * @return mixed
     */
    public function getQuery();

    public function getIdentifier(): string;

    public function getRawResult(): RawResultInterface;
}
