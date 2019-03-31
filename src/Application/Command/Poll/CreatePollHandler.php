<?php

declare(strict_types=1);

namespace App\Application\Command\Poll;

use App\Domain\Poll\Poll;
use App\Domain\Poll\PollId;
use App\Domain\Poll\PollRepository;

class CreatePollHandler
{
    private $polls;


    public function __construct(PollRepository $polls)
    {
        $this->polls = $polls;
    }


    public function __invoke(CreatePoll $command) : void
    {
        $poll = Poll::create(
            new PollId($command->id),
            $command->name,
            $command->topic,
            $command->description
        );

        $this->polls->save($poll);
    }
}
