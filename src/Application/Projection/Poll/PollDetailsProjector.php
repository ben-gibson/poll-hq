<?php

declare(strict_types=1);

namespace App\Application\Projection\Poll;

use App\Application\ReadModel\Poll\PollDetails;
use App\Application\ReadModel\Poll\PollRepository;
use App\Domain\Event\EventSubscriber;
use App\Domain\Poll\Event\PollCreated;

class PollDetailsProjector implements EventSubscriber
{
    private $pollRepository;


    public function __construct(PollRepository $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }


    /**
     * @inheritDoc
     */
    public static function eventSubscriptions() : iterable
    {
        yield PollCreated::class => 'projectPollCreated';
    }


    public function projectPollCreated(PollCreated $event) : void
    {
        $details = new PollDetails($event->id(), $event->name(), $event->topic(), $event->description());

        $this->pollRepository->saveDetails($details);
    }
}
