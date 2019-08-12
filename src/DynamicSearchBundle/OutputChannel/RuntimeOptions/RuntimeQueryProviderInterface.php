<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeQueryProviderInterface
{
    /**
     * @return string
     */
    public function getUserQuery();

    /**
     * @return string|null
     */
    public function getUserLocale();

    /**
     * @return string
     */
    public function getQueryIdentifier();
}
