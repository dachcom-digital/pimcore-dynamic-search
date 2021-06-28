<?php

namespace DynamicSearchBundle\Filter;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\Context\OutputChannelContextInterface;
use DynamicSearchBundle\OutputChannel\Query\Result\RawResultInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterInterface
{
    /**
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver);

    public function setOptions(array $options): void;

    public function setName(string $name): void;

    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher): void;

    public function setOutputChannelContext(OutputChannelContextInterface $outputChannelContext): void;

    public function supportsFrontendView(): bool;

    /**
     * @param mixed $query
     *
     * @return mixed $query
     */
    public function enrichQuery($query);

    /**
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
