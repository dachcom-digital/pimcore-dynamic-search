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

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return RawResultInterface
     */
    public function getRawResult();
}
