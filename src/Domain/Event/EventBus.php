<?php

declare(strict_types=1);

namespace App\Domain\Event;

use Krixon\DomainEvent\Event;

interface EventBus
{
    public function dispatch(Event $event) : void;
}
