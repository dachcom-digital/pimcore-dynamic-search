<?php

namespace DynamicSearchBundle\Serializer;

use DynamicSearchBundle\OutputChannel\Result\Document\DocumentInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OutputChannelDocumentNormalizer extends AbstractNormalizer
{
    /**
     * @param DocumentInterface $document
     * @param null              $format
     * @param array             $context
     *
     * @return mixed
     */
    public function normalize($document, $format = null, array $context = [])
    {
        $context = array_merge($context, [
            'dynamic_search_context'         => true,
            'dynamic_search_context_options' => [
                'context_name'        => $document->getContextName(),
                'output_channel'      => $document->getOutputChannelName(),
                'document_definition' => $document->getOutputDocumentDefinition()
            ]
        ]);

        return $this->serializer->normalize($document->getHit(), $format, $context);
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof DocumentInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        throw new LogicException(sprintf('Cannot denormalize with "%s".', OutputChannelResultInterface::class));
    }
}