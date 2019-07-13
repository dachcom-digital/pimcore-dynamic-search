<?php

namespace DynamicSearchBundle\OutputChannel\RuntimeOptions;

interface RuntimeOptionsProviderInterface
{
    public function setDefaultOptions(array $options = []);

    /**
     * @return int
     */
    public function getUserQuery();

    /**
     * @return string
     */
    public function getQueryIdentifier();

    /**
     * @return int
     */
    public function getCurrentPage();

    /**
     * @return int
     */
    public function getMaxPerPage();

    /**
     * @return array
     */
    public function getAdditionalParameter();
}
