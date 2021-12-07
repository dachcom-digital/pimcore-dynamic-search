<?php

namespace DynamicSearchBundle\Resource;

interface ResourceScaffolderContainerInterface
{
    public function getScaffolder(): ResourceScaffolderInterface;

    public function getIdentifier(): string;
}
