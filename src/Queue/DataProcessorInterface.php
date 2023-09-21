<?php

namespace DynamicSearchBundle\Queue;

interface DataProcessorInterface
{
    public function process(array $options): void;
}
