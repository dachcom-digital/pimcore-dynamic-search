<?php

namespace DynamicSearchBundle\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface OptionAwareResolverInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);
}
