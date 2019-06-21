<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface DataProviderInterface extends ProviderInterface, OptionAwareResolverInterface
{
}