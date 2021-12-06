<?php

namespace DynamicSearchBundle\Index;

interface IndexFieldInterface
{
    public function build(string $name, mixed $data, array $configuration = []): mixed;
}
