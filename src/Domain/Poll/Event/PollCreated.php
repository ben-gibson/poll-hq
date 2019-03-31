<?php

declare(strict_types=1);

namespace App\Domain\Poll\Event;

class PollCreated extends PollEvent
{
    private const TYPE = 'created';

    private $id;
    private $name;
    private $topic;
    private $description;


    public function __construct(string $id, string $name, string $topic, ?string $description)
    {
        parent::__construct();

        $this->id          = $id;
        $this->name        = $name;
        $this->topic       = $topic;
        $this->description = $description;
    }


    public function id() : string
    {
        return $this->id;
    }


    public function name() : string
    {
        return $this->name;
    }


    public function topic() : string
    {
        return $this->topic;
    }


    public function description() : ?string
    {
        return $this->description;
    }


    protected static function type() : string
    {
        return self::TYPE;
    }
}
