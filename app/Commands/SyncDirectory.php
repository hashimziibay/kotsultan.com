<?php

namespace App\Commands;

use App\Libraries\WordPressMigrator;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncDirectory extends BaseCommand
{
    protected $group = 'Directory';
    protected $name = 'directory:sync';
    protected $description = 'Synchronise the read-only legacy directory source into the application database.';

    public function run(array $params)
    {
        $migrator = new WordPressMigrator();
        $stats = $migrator->migrate();
        CLI::write(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
