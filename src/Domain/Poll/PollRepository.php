<?php

declare(strict_types=1);

namespace App\Domain\Poll;

use Krixon\DomainEvent\Sourcing\EventStream;
use Krixon\DomainEvent\Sourcing\EventStreamId;
use Krixon\DomainEvent\Storage\EventStreamStore;

class PollRepository
{
    private $eventStore;


    public function __construct(EventStreamStore $eventStreamStore)
    {
        $this->eventStore = $eventStreamStore;
    }


    public function save(Poll $poll) : void
    {
        // What about events for entities under this aggregate, how do we capture and store them?

        $id     = new EventStreamId($poll->id()->id());
        $stream = new EventStream($id, $poll->recordedEvents());

        $this->eventStore->appendEvents($stream);
    }
}
