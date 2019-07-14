<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Normalizer\DocumentNormalizerInterface;
use DynamicSearchBundle\Normalizer\ResourceNormalizerInterface;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\Provider\DataProviderInterface;
use DynamicSearchBundle\Provider\IndexProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ContextDataInterface
{
    /*
     * Index: Complete data indexing
     * (full context indexing for example)
     */
    const CONTEXT_DISPATCH_TYPE_INDEX = 'index';

    /*
     * Insert: Add single resource to index
     */
    const CONTEXT_DISPATCH_TYPE_INSERT = 'insert';

    /*
     * Update: Update single resource in index
     */
    const CONTEXT_DISPATCH_TYPE_UPDATE = 'update';

    /*
     * Delete: Remove single resource from index
     */
    const CONTEXT_DISPATCH_TYPE_DELETE = 'delete';

    /*
     * Load data from index
     * and pass query data to output channel
     */
    const CONTEXT_DISPATCH_TYPE_FETCH = 'fetch';

    /*
     * Allowed dispatch types for queue
     */
    const ALLOWED_QUEUE_DISPATCH_TYPES = ['insert', 'update', 'delete'];

    /*
     * Available Output Channels
     * to fetch query data from index
     */
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
    public function getContextDispatchType();

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function updateRuntimeValue(string $key, $value);

    /**
     * @return array
     */
    public function getRuntimeValues();

    /**
     * @return string
     */
    public function getDataProviderName();

    /**
     * @return string
     */
    public function getIndexProviderName();

    /**
     * @return string
     */
    public function getResourceNormalizerName();

    /**
     * @param DataProviderInterface $dataProvider
     * @param string                $providerBehaviour
     *
     * @return array
     *
     * @throws ContextConfigurationException
     */
    public function getDataProviderOptions(DataProviderInterface $dataProvider, string $providerBehaviour);

    /**
     * @param IndexProviderInterface $indexProvider
     *
     * @return array
     *
     * @throws ContextConfigurationException
     */
    public function getIndexProviderOptions(IndexProviderInterface $indexProvider);

    /**
     * @param ResourceNormalizerInterface $resourceNormalizer
     *
     * @return array
     *
     * @throws ContextConfigurationException
     */
    public function getResourceNormalizerOptions(ResourceNormalizerInterface $resourceNormalizer);

    /**
     * @param DocumentNormalizerInterface $documentNormalizer
     * @param string                      $outputChannelName
     *
     * @return array
     *
     * @throws ContextConfigurationException
     */
    public function getOutputChannelDocumentNormalizerOptions(DocumentNormalizerInterface $documentNormalizer, string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return string|null
     */
    public function getOutputChannelNormalizerName(string $outputChannelName);

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
     *
     * @throws ContextConfigurationException
     */
    public function getOutputChannelOptions(string $outputChannelName, OutputChannelInterface $outputChannel, ?OptionsResolver $optionsResolver = null);
}
