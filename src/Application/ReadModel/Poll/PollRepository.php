<?php

declare(strict_types=1);

namespace App\Application\ReadModel\Poll;

interface PollRepository
{
    public function fetchById(string $id) : ?PollDetails;
    public function saveDetails(PollDetails $details) : void;


    /**
     * @return PollDetails[]
     */
    public function fetchAll(?int $limit = null) : array;
}
