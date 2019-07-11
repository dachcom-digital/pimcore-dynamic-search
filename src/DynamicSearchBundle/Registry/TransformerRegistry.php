<?php

namespace DynamicSearchBundle\Registry;

use DynamicSearchBundle\Transformer\DocumentTransformerInterface;
use DynamicSearchBundle\Transformer\FieldTransformerInterface;

class TransformerRegistry implements TransformerRegistryInterface
{
    /**
     * @var array
     */
    protected $documentTransformer;

    /**
     * @var array
     */
    protected $fieldTransformer;

    /**
     * @param        $service
     * @param string $identifier
     */
    public function registerDocumentTransformer($service, string $identifier)
    {
        if (!in_array(DocumentTransformerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), DocumentTransformerInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->documentTransformer[$identifier] = $service;
    }

    /**
     * @param        $service
     * @param string $identifier
     * @param string $dataTransformer
     */
    public function registerFieldTransformer($service, string $identifier, string $dataTransformer)
    {
        if (!in_array(FieldTransformerInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), FieldTransformerInterface::class, implode(', ', class_implements($service)))
            );
        }

        if (!isset($this->fieldTransformer[$dataTransformer])) {
            $this->fieldTransformer[$dataTransformer] = [];
        }

        $this->fieldTransformer[$dataTransformer][$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldTransformer(string $dispatchTransformerName, string $identifier)
    {
        return isset($this->fieldTransformer[$dispatchTransformerName]) && isset($this->fieldTransformer[$dispatchTransformerName][$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldTransformer(string $dispatchTransformerName, string $identifier)
    {
        return $this->fieldTransformer[$dispatchTransformerName][$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDocumentTransformers()
    {
        return $this->documentTransformer;
    }
}