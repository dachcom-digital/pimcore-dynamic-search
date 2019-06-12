<?php

namespace DynamicSearchBundle\Service;

use Doctrine\DBAL\Connection;

interface LongProcessServiceInterface
{
    public function boot();

    public function shutdown();

}
