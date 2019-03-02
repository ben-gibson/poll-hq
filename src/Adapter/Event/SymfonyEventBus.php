<?php

declare(strict_types=1);

namespace App\Adapter\Event;

use App\Domain\Event\EventBus;
use Krixon\DomainEvent\Event;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyEventBus implements EventBus
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }


    public function dispatch(Event $event) : void
    {
        $this->bus->dispatch($event);
    }
}
