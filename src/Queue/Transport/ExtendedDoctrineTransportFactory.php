<?php

namespace DynamicSearchBundle\Queue\Transport;

use Doctrine\Persistence\ConnectionRegistry;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ExtendedDoctrineTransportFactory implements TransportFactoryInterface
{
    public function __construct(private ConnectionRegistry $registry)
    {}

    public function createTransport(#[\SensitiveParameter] string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        unset($options['transport_name'], $options['use_notify']);

        $configuration = ExtendedDoctrineConnection::buildConfiguration($dsn, $options);

        try {
            $driverConnection = $this->registry->getConnection($configuration['connection']);
        } catch (\InvalidArgumentException $e) {
            throw new TransportException('Could not find Doctrine connection from Messenger DSN.', 0, $e);
        }

        $connection = new ExtendedDoctrineConnection($configuration, $driverConnection);

        return new DoctrineTransport($connection, $serializer);
    }

    public function supports(#[\SensitiveParameter] string $dsn, array $options): bool
    {
        return str_starts_with($dsn, 'microsecs-doctrine://');
    }
}
