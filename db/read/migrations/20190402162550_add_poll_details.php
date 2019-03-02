<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class AddPollDetails extends AbstractMigration
{
    public function change() : void
    {
        $table = $this->table('poll_details', ['id' => false, 'primary_key' => 'id']);

        $table
            ->addColumn('id', 'uuid')
            ->addColumn('name', 'text')
            ->addColumn('topic', 'text')
            ->addColumn('description', 'text', ['null' => true])
            ->create();
    }
}
