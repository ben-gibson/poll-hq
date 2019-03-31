<?php

declare(strict_types=1);

namespace App\Adapter\MessageBus;

use App\Adapter\Event\EventTransportSerializer;
use App\Adapter\Event\Exception\UnsupportedEnvelope;
use Krixon\DomainEvent\Event;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * Determines which serializer is most appropriate for the given message.
 *
 * This is required because only one serializer can be configured for all message buses however I want to the use the
 * dedicated hydrator that comes with the events library to serialise events as this doesn't depend on specifically
 * named getter or setter methods and also doesn't depend on the events FQCN which would otherwise be problematic if
 * it was changed. In Symfony 4.3 the default transport serializer has been updated to use PHP's
 * native serialize and unserialize methods which solves some of the above issues however it ties message consumers to
 * PHP and would still be problematic when FQCNs are changed.
 *
 * @see https://symfony.com/blog/new-in-symfony-4-3-native-php-serialization-for-messenger
 */
class DelegatingTransportSerializer implements SerializerInterface
{
    private $eventSerializer;
    private $defaultSerializer;


    public function __construct(EventTransportSerializer $eventSerializer, SerializerInterface $defaultSerializer)
    {
        $this->eventSerializer   = $eventSerializer;
        $this->defaultSerializer = $defaultSerializer;
    }


    /**
     * @inheritDoc
     */
    public function decode(array $encodedEnvelope) : Envelope
    {
        try {
            return $this->eventSerializer->decode($encodedEnvelope);
        } catch (UnsupportedEnvelope $exception) {
            return $this->defaultSerializer->decode($encodedEnvelope);
        }
    }


    /**
     * @inheritDoc
     */
    public function encode(Envelope $envelope) : array
    {
        if ($envelope->getMessage() instanceof Event) {
            return $this->eventSerializer->encode($envelope);
        }

        return $this->defaultSerializer->encode($envelope);
    }
}
