<?php

namespace DynamicSearchBundle\Filter;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterInterface
{
    /**
     * @param OptionsResolver $resolver
     *
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher);

    /**
     * @param OutputChannelContextInterface $outputChannelContext
     */
    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext);

    /**
     * @return bool
     */
    public function supportsFrontendView(): bool;

    /**
     * @param mixed $query
     *
     * @return mixed $query
     */
    public function enrichQuery($query);

    /**
     * @param RawResultInterface $rawResult
     *
     * @return mixed
     */
    public function findFilterValueInResult(RawResultInterface $rawResult);

    /**
     * @param RawResultInterface $rawResult
     * @param mixed              $filterValues
     * @param mixed              $query
     *
     * @return mixed
     */
    public function buildViewVars(RawResultInterface $rawResult, $filterValues, $query);
}
