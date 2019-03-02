<?php

declare(strict_types=1);

namespace App\Adapter\Persistence\Doctrine;

use App\Domain\Event\EventBus;
use Doctrine\DBAL\Connection;
use Krixon\DomainEvent\Event;
use Krixon\DomainEvent\Sourcing\EventStream;
use Krixon\DomainEvent\Sourcing\EventStreamId;
use Krixon\DomainEvent\Storage\EventStreamStore;
use Krixon\DomainEvent\Storage\Exception\EventStreamNotFound;
use Krixon\DomainEvent\Storage\Hydration\EventHydrator;
use Symfony\Component\Intl\Exception\NotImplementedException;
use function sprintf;

class DBALEventStreamStore implements EventStreamStore
{
    private $connection;
    private $eventHydrator;
    private $eventBus;


    public function __construct(Connection $connection, EventHydrator $eventHydrator, EventBus $eventBus)
    {
        $this->connection    = $connection;
        $this->eventHydrator = $eventHydrator;
        $this->eventBus      = $eventBus;
    }


    public function eventStream(EventStreamId $eventStreamId) : EventStream
    {
        $qb = $this->connection->createQueryBuilder();

        $qb
            ->select('*')
            ->from('events')
            ->where('stream_id = :streamId')
            ->setParameter('streamId', $this->connection->convertToDatabaseValue($eventStreamId, 'event_stream_id'))
            ->getSQL();

        $events = $this->connection->project($qb->getSQL(), $qb->getParameters(), function (array $row) {
            $row['occurredOn'] = $this->connection->convertToPHPValue($row['occurredOn'], 'datetime');

            return $this->eventHydrator->hydrate($row);
        });

        if (empty($events)) {
            throw new EventStreamNotFound(sprintf("Event stream '%s' not found.", $eventStreamId->id()));
        }

        return new EventStream($eventStreamId, $events, 0);
    }


    /**
     * @inheritDoc
     */
    public function eventStreamSinceEventNumber(EventStreamId $eventStreamId, $eventNumber) : EventStream
    {
        throw new NotImplementedException('Not implemented yet!');
    }


    public function appendEvents(EventStream $eventStream) : void
    {
        foreach ($eventStream->events() as $event) {
            $this->appendEvent($eventStream->id(), $event);
            $this->eventBus->dispatch($event);
        }
    }


    private function appendEvent(EventStreamId $id, Event $event) : void
    {
        $body               = $this->eventHydrator->dehydrate($event);
        $body['occurredOn'] = $this->connection->convertToDatabaseValue($event->occurredOn(), 'datetime');

        $streamId = $this->connection->convertToDatabaseValue($id, 'event_stream_id');
        $occurred = $this->connection->convertToDatabaseValue($event->occurredOn(), 'datetime');
        $body     = $this->connection->convertToDatabaseValue($body, 'json');

        $this->connection->createQueryBuilder()
            ->insert('events')
            ->values(
                [
                    'stream_id'   => ':streamId',
                    'type'        => ':type',
                    'version'     => ':version',
                    'occurred_on' => ':occurredOn',
                    'body'        => ':body',
                ]
            )
            ->setParameter('streamId', $streamId)
            ->setParameter('type', $event->eventType())
            ->setParameter('version', $event->eventVersion())
            ->setParameter('occurredOn', $occurred)
            ->setParameter('body', $body)
            ->execute();
    }


    public function purge() : void
    {
        throw new NotImplementedException('This event store cannot be purged!');
    }
}
