<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeQueryProviderInterface
{
    /**
     * @return string
     */
    public function getUserQuery();

    /**
     * @return string
     */
    public function getQueryIdentifier();
}
