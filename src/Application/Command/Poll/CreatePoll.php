<?php

declare(strict_types=1);

namespace App\Application\Command\Poll;

use App\Application\Command\Command;

class CreatePoll implements Command
{
    public $id;
    public $name;
    public $topic;
    public $description;
    public $responseTarget;
    public $openDate;
    public $closeDate;
}
