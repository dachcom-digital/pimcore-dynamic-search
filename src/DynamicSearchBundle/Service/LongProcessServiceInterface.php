<?php

namespace DynamicSearchBundle\Service;

interface LongProcessServiceInterface
{
    public function boot(): void;

    public function shutdown(): void;
}
