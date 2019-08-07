<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface OutputChannelInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param OutputChannelContextInterface $outputChannelContext
     */
    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext);

    /**
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher);

    /**
     * @return mixed
     */
    public function getQuery();

    /**
     * @param mixed $query
     *
     * @return mixed
     */
    public function getResult($query);

    /**
     * @param mixed $result
     *
     * @return int
     */
    public function getHitCount($result);
}
