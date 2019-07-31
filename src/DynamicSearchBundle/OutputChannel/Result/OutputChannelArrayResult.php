<?php

namespace DynamicSearchBundle\OutputChannel\Result;

class OutputChannelArrayResult extends OutputChannelResult implements OutputChannelArrayResultInterface
{
    /**
     * @var array
     */
    protected $result;

    /**
     * {@inheritdoc}
     */
    public function setResult(array $result)
    {
        return $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->result;
    }
}
