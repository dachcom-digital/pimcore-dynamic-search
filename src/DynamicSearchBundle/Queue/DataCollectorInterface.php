<?php

namespace DynamicSearchBundle\Queue;

interface DataCollectorInterface
{
    /**
     * @param string $contextName
     * @param string $dispatcher
     * @param array  $options
     *
     * @return mixed
     */
    public function addToQueue(string $contextName, string $dispatcher, string $type, int $id, array $options = []);
}