<?php

namespace DynamicSearchBundle\Context;

use DynamicSearchBundle\Exception\ContextConfigurationException;
use DynamicSearchBundle\Exception\UnresolvedContextConfigurationException;
use DynamicSearchBundle\Service\OptionAwareResolverInterface;

interface ContextDataInterface
{
    const DATA_PROVIDER_OPTIONS = 'data_provider_options';

    const INDEX_PROVIDER_OPTIONS = 'index_provider_options';

    const DATA_TRANSFORMER_OPTIONS = 'data_transformer_options';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDataProvider();

    /**
     * @return string
     */
    public function getIndexProvider();

    /**
     * @return array
     * @throws UnresolvedContextConfigurationException
     */
    public function getDataProviderOptions();

    /**
     * @return array
     * @throws UnresolvedContextConfigurationException
     */
    public function getIndexProviderOptions();

    public function getDocumentOptionsConfig();

    public function getDocumentFieldsConfig();

    /**
     * @param OptionAwareResolverInterface $resolver
     * @param string                       $providerType
     *
     * @return $this
     * @throws ContextConfigurationException
     */
    public function assertValidContextProviderOptions(OptionAwareResolverInterface $resolver, string $providerType);
}
