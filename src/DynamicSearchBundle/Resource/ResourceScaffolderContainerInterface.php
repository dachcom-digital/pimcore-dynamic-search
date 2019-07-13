<?php

namespace DynamicSearchBundle\Resource;

interface ResourceScaffolderContainerInterface
{
    /**
     * @return ResourceScaffolderInterface
     */
    public function getScaffolder();

    /**
     * @return string
     */
    public function getIdentifier();
}
