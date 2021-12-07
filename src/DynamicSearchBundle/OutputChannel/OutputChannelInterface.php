<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\SearchContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface OutputChannelInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    public function setOptions(array $options): void;

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void;

    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher): void;

    /**
     * @throws \Exception
     */
    public function getQuery(): mixed;

    /**
     * @throws \Exception
     */
    public function getResult(SearchContainerInterface $searchContainer): SearchContainerInterface;
}
