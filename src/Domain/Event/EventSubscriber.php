<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface EventSubscriber
{
    /**
     * Returns an iterable describing the subscriptions for this subscriber.
     *
     * At the simplest level, to subscribe to all events which will be passed via the __invoke() method:
     *
     * yield '*';
     *
     * To map a subscription to a specific method rather than __invoke():
     *
     * yield '*' => 'someMethodName';
     *
     * To subscribe to a specific event type:
     *
     * yield SomeEvent::class;
     *
     * @return mixed[]
     */
    public static function eventSubscriptions() : iterable;
}
