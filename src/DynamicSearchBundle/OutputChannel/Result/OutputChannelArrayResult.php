<?php

namespace DynamicSearchBundle\OutputChannel\Result;

class OutputChannelArrayResult extends OutputChannelResult implements OutputChannelArrayResultInterface
{
    protected array $result = [];

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
