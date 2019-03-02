<?php

declare(strict_types=1);

namespace App\Adapter\Event;

use App\Adapter\Event\Exception\UnsupportedEnvelope;
use Krixon\DateTime\DateTime;
use Krixon\DomainEvent\Storage\Hydration\EventHydrator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\InvalidArgumentException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use function array_merge;
use function is_array;
use function get_class;
use function json_decode;
use function json_encode;
use function strlen;
use function strpos;
use function substr;

class EventTransportSerializer implements SerializerInterface
{
    private const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

    private $serializer;
    private $eventHydrator;


    public function __construct(SymfonySerializerInterface $serializer, EventHydrator $eventHydrator)
    {
        $this->serializer    = $serializer;
        $this->eventHydrator = $eventHydrator;
    }


    /**
     * @inheritDoc
     */
    public function decode(array $encodedEnvelope) : Envelope
    {
        if (empty($encodedEnvelope['body']) || empty($encodedEnvelope['headers'])) {
            throw new InvalidArgumentException('Encoded envelope should have at least a "body" and some "headers".');
        }

        if (empty($encodedEnvelope['headers']['type'])) {
            throw new InvalidArgumentException('Encoded envelope does not have a "type" header.');
        }

        $body = json_decode($encodedEnvelope['body'], true);

        if (!is_array($body) || !isset($body['occurredOn']) || !isset($body['eventType'])) {
            throw new UnsupportedEnvelope();
        }

        $stamps = $this->decodeStamps($encodedEnvelope);

        $body['occurredOn'] = DateTime::fromFormat(\DateTime::ISO8601, $body['occurredOn']);
        $message            = $this->eventHydrator->hydrate($body);

        return new Envelope($message, ...$stamps);
    }



    /**
     * @inheritDoc
     */
    public function encode(Envelope $envelope) : array
    {
        $headers = ['type' => get_class($envelope->getMessage())] + $this->encodeStamps($envelope);

        // TODO: handle errors.
        return [
            'body'    => json_encode($this->eventHydrator->dehydrate($envelope->getMessage())),
            'headers' => $headers,
        ];
    }


    /**
     * @param mixed[] $encodedEnvelope
     *
     * @return mixed[]
     */
    private function decodeStamps(array $encodedEnvelope) : array
    {
        $stamps = [];

        foreach ($encodedEnvelope['headers'] as $name => $value) {
            if (strpos($name, self::STAMP_HEADER_PREFIX) !== 0) {
                continue;
            }

            $stamps[] = $this->serializer->deserialize(
                $value,
                substr($name, strlen(self::STAMP_HEADER_PREFIX)) . '[]',
                'json'
            );
        }

        if ($stamps) {
            $stamps = array_merge(...$stamps);
        }

        return $stamps;
    }


    /**
     * @return mixed[]
     */
    private function encodeStamps(Envelope $envelope) : array
    {
        $allStamps = $envelope->all();

        if (!$allStamps) {
            return [];
        }

        $headers = [];

        foreach ($allStamps as $class => $stamps) {
            $header           = self::STAMP_HEADER_PREFIX . $class;
            $headers[$header] = $this->serializer->serialize($stamps, 'json');
        }

        return $headers;
    }
}
