<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
