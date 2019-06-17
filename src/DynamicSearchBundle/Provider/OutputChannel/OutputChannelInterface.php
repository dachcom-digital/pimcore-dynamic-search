<?php

namespace DynamicSearchBundle\Provider\OutputChannel;

use DynamicSearchBundle\Context\ContextDataInterface;

interface OutputChannelInterface
{
    /**
     * @param ContextDataInterface $context
     * @param array                $options
     *
     * @return mixed
     */
    public function execute(ContextDataInterface $context, array $options = []);

}