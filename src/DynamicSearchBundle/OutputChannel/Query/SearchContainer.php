<?php

namespace DynamicSearchBundle\OutputChannel\Query;

use DynamicSearchBundle\OutputChannel\Query\Result\RawResult;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;

class SearchContainer implements SearchContainerInterface
{
    /**
     * @var RawResultInterface
     */
    public $result;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var mixed
     */
    protected $query;

    /**
     * @param string $identifier
     * @param mixed  $query
     */
    public function __construct(string $identifier, $query)
    {
        $this->result = new RawResult();
        $this->identifier = $identifier;
        $this->query = $query;
    }

    /**
     * {@inheritDoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getRawResult()
    {
        return $this->result;
    }
}
