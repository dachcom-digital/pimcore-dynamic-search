<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataProviderInterface extends ProviderInterface
{
    const PROVIDER_BEHAVIOUR_FULL_DISPATCH = 'full_dispatch';

    const PROVIDER_BEHAVIOUR_SINGLE_DISPATCH = 'single_dispatch';

    /**
     * @param OptionsResolver $resolver
     * @param string          $providerBehaviour
     */
    public function configureOptions(OptionsResolver $resolver, string $providerBehaviour);

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @return mixed resource
     */
    public function checkUntrustedResourceProxy(ContextDataInterface $contextData, $resource);

    /**
     * @param ContextDataInterface $contextData
     * @param mixed                $resource
     *
     * @return bool
     */
    public function validateUntrustedResource(ContextDataInterface $contextData, $resource);

    /**
     * @param ContextDataInterface $contextData
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideAll(ContextDataInterface $contextData);

    /**
     * @param ContextDataInterface  $contextData
     * @param ResourceMetaInterface $resourceMeta
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideSingle(ContextDataInterface $contextData, ResourceMetaInterface $resourceMeta);
}
