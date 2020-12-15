<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface OutputChannelInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

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
     * @throws \Exception
     */
    public function getQuery();

    /**
     * @param SearchContainerInterface $searchContainer
     *
     * @return SearchContainerInterface
     * @throws \Exception
     */
    public function getResult(SearchContainerInterface $searchContainer): SearchContainerInterface;
}
