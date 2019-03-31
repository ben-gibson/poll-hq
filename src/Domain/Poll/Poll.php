<?php

declare(strict_types=1);

namespace App\Domain\Poll;

use App\Domain\Event\Exception\IncompatibleEvent;
use App\Domain\Event\Exception\UnknownEvent;
use App\Domain\Poll\Event\PollCreated;
use App\Domain\Poll\Event\PollEvent;
use Krixon\DomainEvent\Event;
use Krixon\DomainEvent\Recording\RecordedEventContainer;
use Krixon\DomainEvent\Recording\RecordsEventsInternally;
use Krixon\DomainEvent\Sourcing\EventStream;

class Poll implements RecordedEventContainer
{
    use RecordsEventsInternally;

    private $id;
    private $name;
    private $topic;
    private $description;


    private function __construct(PollId $id)
    {
        $this->id = $id;
    }


    public static function create(PollId $id, string $name, string $topic, ?string $description) : self
    {
        $instance = new self($id);

        $event = new PollCreated($id->id(), $name, $topic, $description);

        $instance->recordEvent($event);

        $instance->applyEvent($event);

        return $instance;
    }


    public static function fromStream(EventStream $stream) : self
    {
        $instance = new self(new PollId($stream->id()));

        foreach ($stream as $event) {
            $instance->applyEvent($event);
        }

        return $instance;
    }


    public function id() : PollId
    {
        return $this->id;
    }


    public function name() : string
    {
        return $this->name;
    }


    public function topic() : string
    {
        return $this->topic;
    }


    public function description() : ?string
    {
        return $this->description;
    }


    private function applyEvent(Event $event) : void
    {
        if (!($event instanceof PollEvent)) {
            throw new IncompatibleEvent($event);
        }

        // Hacked in for now.
        if ($event instanceof PollCreated) {
            $this->applyPollCreatedEvent($event);
            return;
        }

        throw new UnknownEvent($event);
    }


    public function applyPollCreatedEvent(PollCreated $event) : void
    {
        $this->name        = $event->name();
        $this->topic       = $event->topic();
        $this->description = $event->description();
    }
}
