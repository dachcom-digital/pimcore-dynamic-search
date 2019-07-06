<?php

namespace DynamicSearchBundle\Document\Definition;

use DynamicSearchBundle\Context\ContextDataInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutputDocumentDefinition implements OutputDocumentDefinitionInterface
{
    /**
     * @var array
     */
    protected $outputFieldDefinitions;

    /**
     * {@inheritDoc}
     */
    public function addOutputFieldDefinition(array $definition)
    {
        $channelVisibility = [];
        foreach (ContextDataInterface::AVAILABLE_OUTPUT_CHANNEL_TYPES as $channel) {
            $channelVisibility[$channel] = true;
        }

        $resolver = new OptionsResolver();
        $resolver->setRequired(['name', 'channel_visibility']);
        $resolver->setAllowedTypes('name', ['string']);
        $resolver->setAllowedTypes('channel_visibility', ['bool[]']);
        $resolver->setDefault('channel_visibility', $channelVisibility);

        $options = $resolver->resolve($definition);

        $options['channel_visibility'] = array_merge($channelVisibility, $options['channel_visibility']);

        $this->outputFieldDefinitions[] = $options;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputFieldDefinitions(): array
    {
        return !is_array($this->outputFieldDefinitions) ? [] : $this->outputFieldDefinitions;
    }
}