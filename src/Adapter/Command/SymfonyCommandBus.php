<?php

declare(strict_types=1);

namespace App\Adapter\Command;

use App\Application\Command\CommandBus;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyCommandBus implements CommandBus
{
    private $commandBus;


    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }


    public function execute(object $command) : void
    {
        $this->commandBus->dispatch($command);
    }
}
