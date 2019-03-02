<?php

declare(strict_types=1);

namespace App\Adapter\MessageBus\Middleware;

use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * TODO: Does it make sense to only run this once the message has been received?
 */
class DBALTransactionManager implements MiddlewareInterface
{
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function handle(Envelope $envelope, StackInterface $stack) : Envelope
    {
        return $this->connection->transactional(static function () use ($envelope, $stack) : Envelope {
            return $stack->next()->handle($envelope, $stack);
        });
    }
}
