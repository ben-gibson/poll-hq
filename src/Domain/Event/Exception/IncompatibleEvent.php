<?php

declare(strict_types=1);

namespace App\Domain\Event\Exception;

use App\Domain\Exception\DomainException;
use Krixon\DomainEvent\Event;
use RuntimeException;
use function sprintf;

/**
 * Thrown when a domain object is provided a incompatible event.
 */
class IncompatibleEvent extends RuntimeException implements DomainException
{
    public function __construct(Event $event)
    {
        parent::__construct(sprintf(
            "The event '%s' is not compatible with the domain object.",
            $event->eventType()
        ));
    }
}
