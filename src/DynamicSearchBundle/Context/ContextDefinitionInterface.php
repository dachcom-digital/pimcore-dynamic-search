<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;

interface ContextDefinitionInterface
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
     * @param string $providerBehaviour
     *
     * @return array
     */
    public function getDataProviderOptions(string $providerBehaviour);

    /**
     * @return array
     */
    public function getIndexProviderOptions();

    /**
     * @return array
     */
    public function getResourceNormalizerOptions();

    /**
     * @param string $outputChannelName
     *
     * @return array
     */
    public function getOutputChannelDocumentNormalizerOptions(string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return array
     *
     * @throws ContextConfigurationException
     */
    public function getOutputChannelOptions(string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return array
     */
    public function getOutputChannelPaginatorOptions(string $outputChannelName);

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
     * @return array
     */
    public function getOutputChannelEnvironment(string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return string|null
     */
    public function getOutputChannelRuntimeQueryProvider(string $outputChannelName);

    /**
     * @param string $outputChannelName
     *
     * @return string|null
     */
    public function getOutputChannelRuntimeOptionsBuilder(string $outputChannelName);
}
