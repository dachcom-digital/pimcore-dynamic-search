<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeOptionsBuilderInterface
{
    /**
     * @param string|null $prefix
     *
     * @return \ArrayObject
     */
    public function buildOptions(?string $prefix);
}
