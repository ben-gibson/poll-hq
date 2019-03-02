<?php

declare(strict_types=1);

namespace App\UI\CLI\Bus;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeCommands extends Command
{
    public function __construct()
    {
        parent::__construct('pollhq:bus:consume:commands');
    }


    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $output->writeln('Consuming commands.');

        $consumeCommand = $this->getApplication()->find('messenger:consume-messages');

        $arguments = [
            'command'  => 'messenger:consume-messages',
            'receiver' => 'amqp_commands',
            '--bus'    => 'messenger.bus.command',
        ];

        $consumeCommand->run(new ArrayInput($arguments), $output);
    }
}
