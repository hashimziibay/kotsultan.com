<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ensure years_of_service holds up to 500 characters on production
 * (older installs still had VARCHAR(50)).
 */
class EnsureWallYearsOfServiceVarchar500 extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('wall_of_kot_sultan', [
            'years_of_service' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('wall_of_kot_sultan', [
            'years_of_service' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }
}
