<?php

namespace DynamicSearchBundle\Resource\Container;

interface IndexFieldContainerInterface
{
    public function getName(): string;

    public function getData(): mixed;

    public function getIndexType(): string;
}
