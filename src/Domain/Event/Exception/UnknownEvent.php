<?php

declare(strict_types=1);

namespace App\Domain\Event\Exception;

use App\Domain\Exception\DomainException;
use Krixon\DomainEvent\Event;
use RuntimeException;
use function sprintf;

/**
 * Thrown when a domain object is provided a compatible event but it is not recognised.
 */
class UnknownEvent extends RuntimeException implements DomainException
{
    public function __construct(Event $event)
    {
        parent::__construct(sprintf("The event '%s' was not recognised.", $event->eventType()));
    }
}
