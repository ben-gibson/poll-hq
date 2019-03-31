<?php

declare(strict_types=1);

namespace App\Adapter\Persistence\Doctrine\Type;

use Krixon\DomainEvent\Sourcing\EventStreamId;

class EventStreamIdType extends StringableType
{
    /**
     * @inheritDoc
     */
    protected function fromString(string $string)
    {
        return new EventStreamId($string);
    }
}
