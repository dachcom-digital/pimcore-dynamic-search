<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface IndexProviderInterface extends ProviderInterface, OptionAwareResolverInterface
{
}