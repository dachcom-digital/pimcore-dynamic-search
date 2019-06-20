<?php

namespace DynamicSearchBundle\OutputChannel;

use DynamicSearchBundle\EventDispatcher\OutputChannelModifierEventDispatcher;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface OutputChannelInterface
{
    /**
     * @param OutputChannelModifierEventDispatcher $eventDispatcher
     */
    public function setEventDispatcher(OutputChannelModifierEventDispatcher $eventDispatcher);

    /**
     * @param RuntimeOptionsProviderInterface $runtimeOptionsProvider
     */
    public function setRuntimeParameterProvider(RuntimeOptionsProviderInterface $runtimeOptionsProvider);

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * @return bool
     */
    public function needsPaginator(): bool;

    /**
     * @return string|null
     */
    public function getPaginatorAdapterClass(): ?string;

    /**
     * @param array $indexProviderOptions
     * @param array $options
     * @param array $contextOptions
     *
     * @return mixed
     */
    public function execute(array $indexProviderOptions, array $options = [], array $contextOptions = []);

}