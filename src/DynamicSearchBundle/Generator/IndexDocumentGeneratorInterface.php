<?php

namespace DynamicSearchBundle\Generator;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Document\IndexDocument;
use DynamicSearchBundle\Exception\SilentException;
use DynamicSearchBundle\Normalizer\Resource\ResourceMetaInterface;
use DynamicSearchBundle\Resource\Container\ResourceContainerInterface;

interface IndexDocumentGeneratorInterface
{
    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param ResourceMetaInterface      $resourceMeta
     * @param ResourceContainerInterface $resourceContainer
     * @param array                      $options
     *
     * @return IndexDocument
     *
     * @throws \Exception
     * @throws SilentException
     */
    public function generate(
        ContextDefinitionInterface $contextDefinition,
        ResourceMetaInterface $resourceMeta,
        ResourceContainerInterface $resourceContainer,
        array $options = []
    );

    /**
     * @param ContextDefinitionInterface $contextDefinition
     * @param array                      $options
     *
     * @return IndexDocument
     *
     * @throws \Exception
     * @throws SilentException
     */
    public function generateWithoutData(
        ContextDefinitionInterface $contextDefinition,
        array $options = []
    );
}
