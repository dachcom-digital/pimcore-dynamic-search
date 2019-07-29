<?php

namespace DynamicSearchBundle\Filter;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;
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
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher);

    /**
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     */
    public function setRuntimeParameterProvider(RuntimeOptionsProviderInterface $runtimeOptionsProvider);

    /**
     * @param array $indexProviderOptions
     */
    public function setIndexProviderOptions(array $indexProviderOptions);

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
     * @param mixed $query
     * @param mixed $result
     *
     * @return mixed|null
     */
    public function buildViewVars($query, $result);

}
