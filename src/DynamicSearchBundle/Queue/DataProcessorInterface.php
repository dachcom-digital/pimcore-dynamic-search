<?php

namespace DynamicSearchBundle\Queue;

interface DataProcessorInterface
{
    /**
     * @param array $options
     */
    public function process(array $options);
}
