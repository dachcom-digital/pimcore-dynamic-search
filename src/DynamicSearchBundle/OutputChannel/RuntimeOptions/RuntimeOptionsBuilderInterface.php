<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeOptionsBuilderInterface
{
    public function buildOptions(?string $prefix): \ArrayObject;
}
