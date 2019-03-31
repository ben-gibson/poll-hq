<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddEvents extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('events', ['id' => false, 'primary_key' => 'stream_id']);

        $table
            ->addColumn('stream_id', 'text')
            ->addColumn('type', 'text')
            ->addColumn('version', 'integer')
            ->addColumn('occurred_on', 'datetime')
            ->addColumn('body', 'jsonb')
            ->create();
    }
}
