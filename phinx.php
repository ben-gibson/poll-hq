<?php

declare(strict_types=1);

$host = getenv('DATABASE_HOST');
$name = getenv('DATABASE_NAME');

if ($host === false || $name === false) {
    throw new RuntimeException('Cannot read database configuration for migrations from environment variables.');
}

$_ENV['PHINX_ENVIRONMENT'] = 'current';

$type = $name === 'poll_hq_event' ? 'event' : 'read';

return [
    'paths' => [
        'migrations' => ["%%PHINX_CONFIG_DIR%%/db/$type/migrations"],
        'seeds'      => ["%%PHINX_CONFIG_DIR%%/db/$type/seeds"],
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database'        => 'current',
        'version_order'           => 'creation',
        'current' => [
            'adapter' => 'pgsql',
            'host'    => $host,
            'name'    => $name,
            'user'    => 'dev',
            'pass'    => 'dev',
            'port'    => 5432,
            'charset' => 'utf8',
        ],
    ],
];
