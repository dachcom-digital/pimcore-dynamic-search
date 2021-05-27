<?php

namespace DynamicSearchBundle\Provider;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProcessCancelledException;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resource\Proxy\ProxyResourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataProviderInterface extends ProviderInterface
{
    const PROVIDER_BEHAVIOUR_FULL_DISPATCH = 'full_dispatch';

    const PROVIDER_BEHAVIOUR_SINGLE_DISPATCH = 'single_dispatch';

    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0. Use {@link DataProviderValidationAwareInterface::validateResource} instead
     *
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @return ProxyResourceInterface|null
     */
    public function checkUntrustedResourceProxy(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @deprecated since 1.0.0 and will be removed in 2.0.0. Use {@link DataProviderValidationAwareInterface::validateResource} instead
     *
     * @param ContextDefinitionInterface $contextDefinition
     * @param mixed                      $resource
     *
     * @return bool
     */
    public function validateUntrustedResource(ContextDefinitionInterface $contextDefinition, $resource);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideAll(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface      $resourceMeta
     *
     * @throws ProviderException
     * @throws ProcessCancelledException
     */
    public function provideSingle(ContextDefinitionInterface $contextDefinition, ResourceMetaInterface $resourceMeta);
}
