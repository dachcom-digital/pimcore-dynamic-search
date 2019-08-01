<?php

namespace DynamicSearchBundle\Filter\Definition;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDefinition implements FilterDefinitionInterface
{
    /**
     * @var array
     */
    protected $filterDefinitions;

    /**
     * {@inheritdoc}
     */
    public function addFilterDefinition(array $definition)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['type', 'name', 'label', 'configuration']);
        $resolver->setAllowedTypes('type', ['string']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('label', ['string', 'null']);
        $resolver->setAllowedTypes('configuration', ['array']);

        $resolver->setDefaults([
            'label'         => null,
            'configuration' => []
        ]);

        $options = $resolver->resolve($definition);

        $this->filterDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterDefinitions(): array
    {
        return !is_array($this->filterDefinitions) ? [] : $this->filterDefinitions;
    }
}
