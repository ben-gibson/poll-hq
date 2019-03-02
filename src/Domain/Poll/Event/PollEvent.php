<?php

declare(strict_types=1);

namespace App\Domain\Poll\Event;

use Krixon\DomainEvent\BaseEvent;

abstract class PollEvent extends BaseEvent
{
    private const NAMESPACE = 'poll.';


    abstract protected static function type() : string;


    public static function eventType() : string
    {
        return self::NAMESPACE . static::type();
    }
}
