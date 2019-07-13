<?php

namespace DynamicSearchBundle\Service;

interface LongProcessServiceInterface
{
    public function boot();

    public function shutdown();
}
