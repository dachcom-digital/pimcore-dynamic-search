<?php

namespace DynamicSearchBundle\Resource\Container;

interface IndexFieldContainerInterface
{
    public function getName(): string;

    public function getData();

    public function getIndexType(): string;
}
