<?php

namespace DynamicSearchBundle\Resolver;

use DynamicSearchBundle\Exception\Resolver\ResourceScaffolderNotFoundException;
use DynamicSearchBundle\Registry\TransformerRegistryInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderContainer;
use DynamicSearchBundle\Resource\ResourceScaffolderContainerInterface;
use DynamicSearchBundle\Resource\ResourceScaffolderInterface;

class ResourceScaffolderResolver implements ResourceScaffolderResolverInterface
{
    protected TransformerRegistryInterface $transformerRegistry;

    public function __construct(TransformerRegistryInterface $transformerRegistry)
    {
        $this->transformerRegistry = $transformerRegistry;
    }

    public function resolve(string $dataProviderName, $resource): ResourceScaffolderContainerInterface
    {
        $validScaffolder = null;
        $validScaffolderName = null;

        foreach ($this->transformerRegistry->getAllResourceScaffolderForDataProvider($dataProviderName) as $scaffolderName => $resourceScaffolder) {
            if ($resourceScaffolder->isApplicable($resource) === true) {
                $validScaffolder = $resourceScaffolder;
                $validScaffolderName = $scaffolderName;

                break;
            }
        }

        if ($validScaffolder instanceof ResourceScaffolderInterface) {
            return new ResourceScaffolderContainer($validScaffolder, $validScaffolderName);
        }

        throw new ResourceScaffolderNotFoundException();
    }
}
