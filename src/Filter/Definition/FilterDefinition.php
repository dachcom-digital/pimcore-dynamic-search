<?php

namespace DynamicSearchBundle\Filter\Definition;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

class FilterDefinition implements FilterDefinitionInterface
{
    protected array $filterDefinitions = [];

    public function addFilterDefinition(array $definition): static
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['type', 'name', 'label', 'configuration']);
        $resolver->setAllowedTypes('type', ['string']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('label', ['string', 'null']);
        $resolver->setAllowedTypes('configuration', ['array']);

        $resolver->setAllowedValues('name', Validation::createIsValidCallable(new Regex('/^[a-z0-9_\-\.]+$/i')));

        $resolver->setDefaults([
            'label'         => null,
            'configuration' => []
        ]);

        $options = $resolver->resolve($definition);

        $this->filterDefinitions[] = $options;

        return $this;
    }

    public function getFilterDefinitions(): array
    {
        return $this->filterDefinitions;
    }
}
