<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ContextDataInterface
{
    const CONTEXT_DISPATCH_TYPE_FETCH = 'fetch';

    const CONTEXT_DISPATCH_TYPE_INDEX = 'index';

    const CONTEXT_DISPATCH_TYPE_INSERT = 'insert';

    const CONTEXT_DISPATCH_TYPE_UPDATE = 'update';

    const CONTEXT_DISPATCH_TYPE_DELETE = 'delete';

    const AVAILABLE_OUTPUT_CHANNEL_TYPES = [
        'autocomplete',
        'suggestions',
        'search'
    ];

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDispatchType();

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function updateRuntimeOption(string $key, $value);

    /**
     * @return array
     */
    public function getRuntimeOptions();

    /**
     * @return string
     */
    public function getDataProviderName();

    /**
     * @return string
     */
    public function getIndexProviderName();

    /**
     * @param DataProviderInterface $dataProvider
     *
     * @return mixed
     * @throws ContextConfigurationException
     */
    public function getDataProviderOptions(DataProviderInterface $dataProvider);

    /**
     * @param IndexProviderInterface $indexProvider
     *
     * @return mixed
     * @throws ContextConfigurationException
     */
    public function getIndexProviderOptions(IndexProviderInterface $indexProvider);

    /**
     * @param string $outputChannelName
     *
     * @return string|null
     */
    public function getOutputChannelServiceName(string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return string|null
     */
    public function getOutputChannelRuntimeOptionsProvider(string $outputChannelName);

    /**
     * @param string                 $outputChannelName
     * @param OutputChannelInterface $outputChannel
     * @param OptionsResolver|null   $optionsResolver
     *
     * @return mixed
     * @throws ContextConfigurationException
     */
    public function getOutputChannelOptions(string $outputChannelName, OutputChannelInterface $outputChannel, ?OptionsResolver $optionsResolver = null);

    /**
     * @return array
     */
    public function getDocumentOptionsConfig();

    /**
     * @return array
     */
    public function getDocumentFieldsConfig();
}