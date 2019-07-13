<?php

namespace DynamicSearchBundle\Index;

interface IndexFieldInterface
{
    /**
     * @param string $name
     * @param mixed  $data
     * @param array  $configuration
     *
     * @return mixed
     */
    public function build(string $name, $data, array $configuration = []);
}
