<?php

namespace DynamicSearchBundle\Serializer;

use DynamicSearchBundle\OutputChannel\OutputChannelResultInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class OutputChannelResultNormalizer extends AbstractNormalizer
{
    /**
     * @param mixed $data
     * @param null  $format
     * @param array $context
     *
     * @return mixed
     */
    public function normalize($data, $format = null, array $context = [])
    {
        if (!$data instanceof OutputChannelResultInterface) {
            return $data;
        }

        $context = array_merge($context, [
            'context_name'           => $data->getContextName(),
            'output_channel_service' => $data->getOutputChannelServiceName(),
            'output_channel'         => $data->getOutputChannelName(),
            'field_definitions'      => $data->getDataTransformerFieldDefinitions(),
        ]);

        return $this->serializer->normalize($data->getResult(), $format, $context);
    }

    /**
     * @param mixed $data
     * @param null  $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OutputChannelResultInterface;
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