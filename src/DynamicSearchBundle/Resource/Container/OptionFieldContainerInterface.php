<?php

namespace DynamicSearchBundle\Resource\Container;

interface OptionFieldContainerInterface
{
    public function getName(): string;

    public function getData();
}
